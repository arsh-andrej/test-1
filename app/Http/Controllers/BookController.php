<?php

namespace App\Http\Controllers;

use App\Auxiliary\AuxMain;
use App\Auxiliary\RequestHandler;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

use App\Models\BookModel;
use App\Models\VisitorModel;

class BookController extends Controller {
	
	public function main(Request $request_OBJ) : string|View {
		
		$bookModelInst_OBJ	=	BookModel::retInst();
		$requestHandler_OBJ	=	RequestHandler::retInst();
		$visitorModelInst_OBJ	=	VisitorModel::retInst();
		
		//то что будет "контентом" на странице(внутри #root_content_out шаблона app.blade.php)
		//актуально при requestMode_STR == 'get'
		$rootContent_HTML	=	'';
		
		//узнать режим тек запроса, get - простой переход по ссылке
		//ajax - POST запрос с данными(например формы) с обязательным ответом в ajax формете
		$requestMode_STR	=	$requestHandler_OBJ->retRequestMode();
		
		$bookData_ARR	=	array(); $isBookFound_BOO	=	false;
		
		//мд тек пользователя
		$visitorUserData_ARR=	array();
		$isVisitorLogIn_BOO	=	false;
		$visitorUserId_INT	=	0;
		
		$visitorModelInst_OBJ->getVisitUserData($isVisitorLogIn_BOO, $visitorUserData_ARR, true);
		
		if ($isVisitorLogIn_BOO) {
			
			$visitorUserId_INT	=	$visitorUserData_ARR[VisitorModel::VISITOR_DEFS_USER_ID];
			
		}
		
		// /books/pathElem_2/pathElem_3
		
		//здесь действие
		$pathElem_2	=	$request_OBJ->segment(2);
		$pathElem_2	=	strlen($pathElem_2) == 0 ? 'show-page' : $pathElem_2;
		
		//здесь мб номер страницы или ID книги
		$pathElem_3	=	$request_OBJ->segment(3);
		$pathElem_3	=	(int) preg_replace('#[^0-9]#', '', $pathElem_3);
		
		//здесь мб номер шага
		$pathElem_4	=	$request_OBJ->segment(4);
		$pathElem_4	=	(int) preg_replace('#[^0-9]#', '', $pathElem_4);
		
		//перехватить ссылку вида /books/1(номер страницы)
		if (preg_match('#^[0-9]+$#', $pathElem_2) > 0) {
			
			$pathElem_3	=	$pathElem_2;
			$pathElem_2	=	'show-page';
			
		}
		
		//...
		
		$requestHandler_OBJ->out_reset();
		
		//есть несколько условий вызова просмотра страницы
		$callShowPage_BOO	=	false;
		
		//
		$get_isPrevStepPass_BOO	=	false;
		
		if ($requestMode_STR == 'get') {
			
			if ($pathElem_2 == 'show-page') {
				
				$callShowPage_BOO	=	true;
				
			} else {
			if ($pathElem_2 == 'show-book') {
				
				$rootContent_HTML	=	$this->retShowBook($pathElem_3);
				
			} else {
			if ($isVisitorLogIn_BOO) {
				
				//если действие "добавить книгу"
				if ($pathElem_2 == 'add-book') {
					
					//показать форму добавления книги
					$rootContent_HTML	=	$this->retShowAddBookForm();
					
				} else {
					
					//получить ID книги, он нужен во всех следующих действиях
					$bookId_INT	=	$pathElem_3;
					$bookModelInst_OBJ->getBookDataById($bookData_ARR, $isBookFound_BOO, $bookId_INT);
					
					if ($requestHandler_OBJ->retIsSuccess()) {
						
						$get_isPrevStepPass_BOO	=	$isBookFound_BOO;
						
						if (!$isBookFound_BOO) { $requestHandler_OBJ->setSoftErrByNum(__LINE__, 55); } 
						
					}
					
				}
				
			} else { $rootContent_HTML	=	$requestHandler_OBJ->retSoftAnswer(51); } } }
			
		}//get
		
		//bookId_INT верен
		if ($get_isPrevStepPass_BOO) {
			
			//если действие "редактировать книгу"
			if ($pathElem_2 == 'edit-book') {
				
				//показать форму редактирования книги
				$rootContent_HTML	=	$this->retShowEditBookForm($bookData_ARR);
				
			} else {
			//если действие "изменить обложку книги"
			if ($pathElem_2 == 'edit-book-cover') {
				
				if ($pathElem_4 == '2') {
					
					//нужно принять post-запрос с картинкой
					$rootContent_HTML	=	$this->retHandleEditBookCoverForm($bookData_ARR, $request_OBJ);
					
				} else { $rootContent_HTML	=	$this->retShowEditBookCoverForm($bookData_ARR); }
				
			} else { $callShowPage_BOO	=	true; } }
			
		}
		
		if ($callShowPage_BOO) {
			
			//поправить номер страницы
			$rootContent_HTML	=	$this->retShowPage($pathElem_3);
			
		}
		
		if ($requestMode_STR == 'ajax') {
			
			//json_decode(file_get_contents('php://input'), true);
			
			//любые действия через ajax-запрос с книгами требуют чтобы чел был залогинен
			if ($isVisitorLogIn_BOO) {
				
				//bookId_INT нужен для добавления обложки, редактирования и удаления книг
				//он не нужен для добавления книги т.к. её ID создаст БД после записи
				
				//если действие "добавить книгу"
				if ($pathElem_2 == 'add-book') {
					
					$this->handleAddBookForm();
					
				} else {
					
					//для других действий нужен bookId_INT
					$bookId_INT	=	$pathElem_3;
					$bookData_ARR	=	array(); $isBookFound_BOO	=	false;
					$bookModelInst_OBJ->getBookDataById($bookData_ARR, $isBookFound_BOO, $bookId_INT);
					
					//если книга с таким ID существует
					if ($requestHandler_OBJ->retIsSuccess()) {
						
						//если действие "удалить книгу"
						if ($pathElem_2 == 'delete-book') {
							
							$this->handleDeleteBook($bookData_ARR);
							
						} else {
						if ($pathElem_2 == 'edit-book') {
							
							$this->handleEditBook($bookData_ARR);
							
						}
						}
						
					}
					
				}//если действие "добавить книгу"
				
			} else { $requestHandler_OBJ->setSoftErrByNum(__LINE__, 51); }
			
		}//ajax
		
		if ($requestMode_STR == 'ajax') {
			
			return json_encode($requestHandler_OBJ->retFormAJAXResult());
			
		} else {
			
			return view('app',
				[
					'rootContent_HTML'	=>	$rootContent_HTML,
					'isVisitorLogIn_INT'=>	((int) $isVisitorLogIn_BOO)
					
				]
			);
			
		}
		
	}//main
	
	public function handleEditBook($bookData_ARR) : void {
		
		//получить объекты нужных классов
		$bookModelInst_OBJ	=	BookModel::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		//проверить имя автора
		$currentFieldName_STR	=	BookModel::BOOK_DEFS_AUTHOR_FULL_NAME; $currentFieldVal_ANY	=	'';
		$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
		$authorFullName_STR	=	$currentFieldVal_ANY;
		
		if ($isPrevStepPass_BOO) {
			
			//проверить название книги
			$currentFieldName_STR	=	BookModel::BOOK_DEFS_TITLE; $currentFieldVal_ANY	=	'';
			$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
			$bookTitle_STR	=	$currentFieldVal_ANY;
			
			if ($isPrevStepPass_BOO) {
				
				//проверить существует ли книга с таким названием
				$isBookTitleExist_BOO	=	false;
				$bookModelInst_OBJ->getIsBookTitleExist($isBookTitleExist_BOO, $bookTitle_STR, $bookData_ARR[BookModel::BOOK_DEFS_ID]);
				
				if ($requestHandlerInst_OBJ->retIsSuccess()) {
					
					$isPrevStepPass_BOO	=	!$isBookTitleExist_BOO;
					
					if ($isPrevStepPass_BOO) {
						
						//проверить год выпуска
						$currentFieldName_STR	=	BookModel::BOOK_DEFS_YEAR; $currentFieldVal_ANY	=	'';
						$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
						$bookYear_INT	=	$currentFieldVal_ANY;
						
						if ($isPrevStepPass_BOO) {
							
							//проверить жанр
							$currentFieldName_STR	=	BookModel::BOOK_DEFS_GENRE_TITLE; $currentFieldVal_ANY	=	'';
							$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
							$genreTitle_STR	=	$currentFieldVal_ANY;
							
						}
						
					} else {
						
						$requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 54);
						$requestHandlerInst_OBJ->setSoftErrFieldName($currentFieldName_STR);
						
					}
					
				}
				
			}
			
		}
		
		if ($isPrevStepPass_BOO) {
			
			//$isPrevStepPass_BOO	=	false;
			
			//проверить количество страниц
			$currentFieldName_STR	=	BookModel::BOOK_DEFS_PAGES_COUNT; $currentFieldVal_ANY	=	'';
			$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
			$pagesCount_INT	=	(int) $currentFieldVal_ANY;
			
			if ($isPrevStepPass_BOO) {
				
				if ($pagesCount_INT > 0) {
					
					$change_ARR	=	array(
						
						BookModel::BOOK_DEFS_AUTHOR_FULL_NAME	=>	array($authorFullName_STR, true),
						BookModel::BOOK_DEFS_TITLE	=>	array($bookTitle_STR, true),
						BookModel::BOOK_DEFS_YEAR	=>	array($bookYear_INT, false),
						BookModel::BOOK_DEFS_GENRE_TITLE	=>	array($genreTitle_STR, true),
						BookModel::BOOK_DEFS_PAGES_COUNT	=>	array($pagesCount_INT, false),
						
					);
					
					$bookModelInst_OBJ->changeBookFieldsById($bookData_ARR[BookModel::BOOK_DEFS_ID], $change_ARR);
					
					if ($requestHandlerInst_OBJ->retIsSuccess()) {
						
						$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
						$requestHandlerInst_OBJ->setSuccessSubmitParams('/books/show-book/'.$bookData_ARR[BookModel::BOOK_DEFS_ID]);
						
					}
					
				} else { $requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 53); }
				
			}
			
		}
		
	}//handleEditBook
	
	public function retShowEditBookForm($bookData_ARR) : view {
		
		$auxMainInst_OBJ	=	AuxMain::retInst();
		
		//ID формы, нужно для работы сборщика значений полей
		$formId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
		
		//адрес обработчика формы
		$requestAddr_STR	=	'/books/edit-book/'.$bookData_ARR[BookModel::BOOK_DEFS_ID].'?mode=ajax';
		
		//в наборе сгенерированные ID полей
		$fieldsIdsSet_ARR	=	array();
		
		//в наборе имена полей в порядке как в $fieldsIdsSet_ARR
		$fieldsNamesSet_ARR	=	array(
			
			BookModel::BOOK_DEFS_AUTHOR_FULL_NAME,
			BookModel::BOOK_DEFS_TITLE,
			BookModel::BOOK_DEFS_YEAR,
			BookModel::BOOK_DEFS_GENRE_TITLE,
			BookModel::BOOK_DEFS_PAGES_COUNT,
			
		);
		
		return view('books_edit_book_form', [
			
			//поле - автор(книги)
			'book_author'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Автор',
					'HTMLFieldDescr_HTML'	=>	'Имя и Фамилия.<br>Буквы русского и Английского алфавита.<br>Знак минус, точка и пробел.<br>Не более 50 символов.',
					'HTMLFieldValue_STR'	=>	$bookData_ARR[BookModel::BOOK_DEFS_AUTHOR_FULL_NAME],
					
				]
				
			),
			
			'book_title'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Название',
					'HTMLFieldDescr_HTML'	=>	'Буквы русского и Английского алфавита.<br>Знак минус, точка и пробел.<br>Без кавычек.<br>Не более 50 символов.',
					'HTMLFieldValue_STR'	=>	$bookData_ARR[BookModel::BOOK_DEFS_TITLE],
					
				]
				
			),
			
			'book_year'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Год выпуска',
					'HTMLFieldDescr_HTML'	=>	'4 цифры',
					'HTMLFieldValue_STR'	=>	$bookData_ARR[BookModel::BOOK_DEFS_YEAR],
					
				]
				
			),
			
			'genre_title'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Жанр',
					'HTMLFieldDescr_HTML'	=>	'Русские и английский буквы, пробел и знак минус.',
					'HTMLFieldValue_STR'	=>	$bookData_ARR[BookModel::BOOK_DEFS_GENRE_TITLE],
					
				]
				
			),
			
			'pages_count'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Количество страниц',
					'HTMLFieldDescr_HTML'	=>	'Только цифры',
					'HTMLFieldValue_STR'	=>	$bookData_ARR[BookModel::BOOK_DEFS_PAGES_COUNT],
					
				]
				
			),
			
			'formId_STR'	=>	$formId_STR,
			'fieldsIdsSet_ARR'	=>	json_encode($fieldsIdsSet_ARR),
			'fieldsNamesSet_ARR'=>	json_encode($fieldsNamesSet_ARR),
			'requestAddr_STR'	=>	$requestAddr_STR,
			'AJAXButtonId_STR'	=>	$auxMainInst_OBJ->retGenHTMLElemId(),
			
		]);
		
	}//retShowEditBookForm
	
	public function handleDeleteBook($bookData_ARR) : void {
	    
		$requestHandler_OBJ	=	RequestHandler::retInst();
		$bookModelInst_OBJ	=	BookModel::retInst();
		
		if ($bookData_ARR[BookModel::BOOK_DEFS_IS_DELETE] == 0) {
			
			$bookModelInst_OBJ->deleteBookById($bookData_ARR[BookModel::BOOK_DEFS_ID]);
			
			if ($requestHandler_OBJ->retIsSuccess()) {
				
				$requestHandler_OBJ->setSuccessByNum(__LINE__, 60);
				
			}
			
		} else { $requestHandler_OBJ->setSoftErrByNum(__LINE__, 59); }
		
	}//handleDeleteBook
	
	//по факту post-запрос, верно
	public function retHandleEditBookCoverForm(array $bookData_ARR, $request_OBJ) : string {
	    
		$requestHandler_OBJ	=	RequestHandler::retInst();
		$bookModelInst_OBJ	=	BookModel::retInst();
		
		$HTMLContent_HTML	=	'';
		$HTMLFieldName_STR	=	'book_cover_file';
		$bookId_INT	=	$bookData_ARR[BookModel::BOOK_DEFS_ID];
		
		//print_r($_FILES);
		
		$isPrevStepPass_BOO	=	false;
		
		$request_OBJ->validate([$HTMLFieldName_STR => 'required|file|mimes:jpg,png,bmp|max:2048',]);
		
		if ($request_OBJ->file($HTMLFieldName_STR)->isValid()) {
			
			$new_coverFullName_STR	=	md5($bookId_INT.'-'.time()).'.'.$request_OBJ->file($HTMLFieldName_STR)->getClientOriginalExtension();
			
			//var_dump($request_OBJ->file($HTMLFieldName_STR));
			Storage::disk('public_uploads')->put($new_coverFullName_STR, file_get_contents($request_OBJ->file($HTMLFieldName_STR)));
			
			if (file_exists('./uploads/'.$new_coverFullName_STR)) {
				
				//файл перемещён успешно, нужно удалить старую картинку если она есть
				$old_fullFileName_STR	=	$bookData_ARR[BookModel::BOOK_DEFS_COVER];
				$old_fullFilePath_STR	=	'./uploads/'.$old_fullFileName_STR;
				
				if ($old_fullFileName_STR != 'default' && strlen($old_fullFileName_STR) == 36 && file_exists($old_fullFilePath_STR)) {
					
					$isPrevStepPass_BOO	=	unlink($old_fullFilePath_STR);
					
				} else { $isPrevStepPass_BOO	=	true; }
				
			} else { $HTMLContent_HTML	=	$requestHandler_OBJ->retHardAnswer(__LINE__); }
			
		} else { $HTMLContent_HTML	=	$requestHandler_OBJ->retSoftAnswer('Загруженный файл не соответствует требованиям'); }
		
		if ($isPrevStepPass_BOO) {
			
			//$isPrevStepPass_BOO	=	false;
			
			$changeBookFields_ARR	=	array(
				
				BookModel::BOOK_DEFS_COVER	=>	array($new_coverFullName_STR, true)
				
			);
			
			$bookModelInst_OBJ->changeBookFieldsById($bookId_INT, $changeBookFields_ARR);
			
			if ($requestHandler_OBJ->retIsSuccess()) {
				
				$HTMLContent_HTML	=	$requestHandler_OBJ->retNormalAnswer('Обложка успешно обновлена');
				
			}
			
		} return $HTMLContent_HTML;
		
	}//retHandleEditBookCoverForm
	
	//показать форму редактирования обложки
	public function retShowEditBookCoverForm(array $bookData_ARR) : View {
		
		$auxMainInst_OBJ	=	AuxMain::retInst();
		$requestHandler_OBJ	=	RequestHandler::retInst();
		
		//получить ID книги
		$bookId_INT	=	$bookData_ARR[BookModel::BOOK_DEFS_ID];
		
		$HTMLContent_HTML	=	$requestHandler_OBJ->retNormalAnswer('Вы редактируете книгу "'.$bookData_ARR[BookModel::BOOK_DEFS_TITLE].'"').'<br>';
		
		//адрес обработчика формы
		$formAction_STR	=	'/books/edit-book-cover/'.$bookId_INT.'/2';
		
		return view('books_edit_book_cover_form', [
			
			//поле - автор(книги)
			'book_cover_file_input'	=>	view('app_std_file_field',
				
				[
					'HTMLFieldId_STR'	=>	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Картинка в качестве обложики для книги',
					'HTMLFieldDescr_HTML'	=>	'Рекомендуемое соотношение сторон //!!!//заполнить',
					'HTMLFieldName_STR'	=>	'book_cover_file',
					
				]
				
			),
			
			'formAction_STR'	=>	$formAction_STR,
			'HTMLContent_HTML'	=>	$HTMLContent_HTML,
			
		]);
		
	}//retShowEditBookCoverForm
	
	//показать форму добавления книги
	public function retShowAddBookForm() : View {
		
		$auxMainInst_OBJ	=	AuxMain::retInst();
		
		//ID формы, нужно для работы сборщика значений полей
		$formId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
		
		//адрес обработчика формы
		$requestAddr_STR	=	'/books/add-book?mode=ajax';
		
		//в наборе сгенерированные ID полей
		$fieldsIdsSet_ARR	=	array();
		
		//в наборе имена полей в порядке как в $fieldsIdsSet_ARR
		$fieldsNamesSet_ARR	=	array(
			
			BookModel::BOOK_DEFS_AUTHOR_FULL_NAME,
			BookModel::BOOK_DEFS_TITLE,
			BookModel::BOOK_DEFS_YEAR,
			BookModel::BOOK_DEFS_GENRE_TITLE,
			BookModel::BOOK_DEFS_PAGES_COUNT,
			
		);
		
		return view('books_add_book_form', [
			
			//поле - автор(книги)
			'book_author'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Автор',
					'HTMLFieldDescr_HTML'	=>	'Имя и Фамилия.<br>Буквы русского и Английского алфавита.<br>Знак минус, точка и пробел.<br>Не более 50 символов.',
					
				]
				
			),
			
			'book_title'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Название',
					'HTMLFieldDescr_HTML'	=>	'Буквы русского и Английского алфавита.<br>Знак минус, точка и пробел.<br>Без кавычек.<br>Не более 50 символов.',
					
				]
				
			),
			
			'book_year'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Год выпуска',
					'HTMLFieldDescr_HTML'	=>	'4 цифры',
					
				]
				
			),
			
			'genre_title'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Жанр',
					'HTMLFieldDescr_HTML'	=>	'Русские и английский буквы, пробел и знак минус.',
					
				]
				
			),
			
			'coverMessage_STR'	=>	'Обложка добавляется на следующем шаге',
			
			'pages_count'	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Количество страниц',
					'HTMLFieldDescr_HTML'	=>	'Только цифры',
					
				]
				
			),
			
			'formId_STR'	=>	$formId_STR,
			'fieldsIdsSet_ARR'	=>	json_encode($fieldsIdsSet_ARR),
			'fieldsNamesSet_ARR'=>	json_encode($fieldsNamesSet_ARR),
			'requestAddr_STR'	=>	$requestAddr_STR,
			'AJAXButtonId_STR'	=>	$auxMainInst_OBJ->retGenHTMLElemId(),
			
		]);
		
	}//retShowAddBookForm
	
	public function handleAddBookForm() : void {
		
		//получить объекты нужных классов
		$bookModelInst_OBJ	=	BookModel::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		//проверить имя автора
		$currentFieldName_STR	=	BookModel::BOOK_DEFS_AUTHOR_FULL_NAME; $currentFieldVal_ANY	=	'';
		$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
		$authorFullName_STR	=	$currentFieldVal_ANY;
		
		if ($isPrevStepPass_BOO) {
			
			//проверить название книги
			$currentFieldName_STR	=	BookModel::BOOK_DEFS_TITLE; $currentFieldVal_ANY	=	'';
			$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
			$bookTitle_STR	=	$currentFieldVal_ANY;
			
			if ($isPrevStepPass_BOO) {
				
				//проверить существует ли книга с таким названием
				$isBookTitleExist_BOO	=	false;
				$bookModelInst_OBJ->getIsBookTitleExist($isBookTitleExist_BOO, $bookTitle_STR);
				
				if ($requestHandlerInst_OBJ->retIsSuccess()) {
					
					$isPrevStepPass_BOO	=	!$isBookTitleExist_BOO;
					
					if ($isPrevStepPass_BOO) {
						
						//проверить год выпуска
						$currentFieldName_STR	=	BookModel::BOOK_DEFS_YEAR; $currentFieldVal_ANY	=	'';
						$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
						$bookYear_INT	=	$currentFieldVal_ANY;
						
						if ($isPrevStepPass_BOO) {
							
							//проверить жанр
							$currentFieldName_STR	=	BookModel::BOOK_DEFS_GENRE_TITLE; $currentFieldVal_ANY	=	'';
							$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
							$genreTitle_STR	=	$currentFieldVal_ANY;
							
						}
						
					} else {
						
						$requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 54);
						$requestHandlerInst_OBJ->setSoftErrFieldName($currentFieldName_STR);
						
					}
					
				}
				
			}
			
		}
		
		if ($isPrevStepPass_BOO) {
			
			//$isPrevStepPass_BOO	=	false;
			
			//проверить количество страниц
			$currentFieldName_STR	=	BookModel::BOOK_DEFS_PAGES_COUNT; $currentFieldVal_ANY	=	'';
			$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
			$pagesCount_INT	=	(int) $currentFieldVal_ANY;
			
			if ($isPrevStepPass_BOO) {
				
				if ($pagesCount_INT > 0) {
					
					$newBookId_INT	=	0;
					$bookModelInst_OBJ->getCreateBook(
						
						$newBookId_INT,
						$authorFullName_STR,
						$bookTitle_STR,
						$bookYear_INT,
						$genreTitle_STR,
						$pagesCount_INT
						
					);
					
					if ($newBookId_INT > 0) {
						
						$requestHandlerInst_OBJ->setSuccessByNum(__LINE__, 1);
						$requestHandlerInst_OBJ->setSuccessSubmitParams('/books/edit-book-cover/'.$newBookId_INT);
						
					} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
					
				} else { $requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 53); }
				
			}
			
		}
		
	}//handleAddBookForm
	
	public function retShowBook($bookId_INT) : string {
		
		$bookModelInst_OBJ	=	BookModel::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$HTMLContent_HTML	=	'';
		
		$bookData_ARR	=	array(); $isFound_BOO	=	false;
		$bookModelInst_OBJ->getBookDataById($bookData_ARR, $isFound_BOO, $bookId_INT);
		
		if ($requestHandlerInst_OBJ->retIsSuccess()) {
			
			if ($bookData_ARR[BookModel::BOOK_DEFS_IS_DELETE] == 0){
				
				//мд книги получен
				$bookCover_STR	=	$bookData_ARR[BookModel::BOOK_DEFS_COVER];
				$bookCoverURL_STR	=	'/uploads/'.$bookCover_STR;
				$booTitle_STR	=	$bookData_ARR[BookModel::BOOK_DEFS_TITLE];
				$bookAuthor_STR	=	$bookData_ARR[BookModel::BOOK_DEFS_AUTHOR_FULL_NAME];
				$bookYear_INT	=	$bookData_ARR[BookModel::BOOK_DEFS_YEAR];
				//$bookGenreTitle_STR	=	$bookData_ARR[BookModel::BOOK_DEFS_GENRE_TITLE];
				$pagesCount_INT	=	$bookData_ARR[BookModel::BOOK_DEFS_PAGES_COUNT];
				
				$HTMLContent_HTML	.=	view('books_show_book_page', [
					
					'bookId_INT'	=>	$bookId_INT,
					'bookCoverURL_STR'	=>	$bookCoverURL_STR,
					'booTitle_STR'	=>	$booTitle_STR,
					'bookAuthor_STR'=>	$bookAuthor_STR,
					'bookYear_INT'	=>	$bookYear_INT,
					'pagesCount_INT'=>	$pagesCount_INT,
					
				]);
				
			} else { $HTMLContent_HTML	=	$requestHandlerInst_OBJ->retSoftAnswer(61); }
			
		} else {
			
			if (!$requestHandlerInst_OBJ->retIsHardErr() && !$isFound_BOO) {
				
				$HTMLContent_HTML	=	$requestHandlerInst_OBJ->retSoftAnswer(55);
				
			}
			
		}
		
		return $HTMLContent_HTML;
		
	}//retShowBook
	
	//показать страницу по её номеру
	public function retShowPage($currPageNum_INT) : string {
		
		//получить объекты нужных классов
		$auxMainInst_OBJ	=	AuxMain::retInst();
		$bookModelInst_OBJ	=	BookModel::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$HTMLContent_HTML	=	'';
		$isPrevStepPass_BOO	=	false;
		$pagesNumsSet_ARR	=	array();
		
		$sortSQL_STR	=	'';
		$filterSQL_STR	=	'';
		
		$booksPerPage_INT	=	3;
		
		$sortAst_ARR	=	array();
		$isSortReq_BOO	=	$auxMainInst_OBJ->getHandleSort($sortAst_ARR, $bookModelInst_OBJ->retAllowSortColsSet());
		
		//если достаточно данных для сортировки
		if ($isSortReq_BOO) {
			
			//print_r($sortAst_ARR);//!!!//
			
			$quoteColsSet_ARR	=	array(BookModel::BOOK_DEFS_AUTHOR_FULL_NAME, BookModel::BOOK_DEFS_GENRE_TITLE);
			$sortSQL_STR	=	$auxMainInst_OBJ->retSortSQLByAst($sortAst_ARR, $quoteColsSet_ARR);
			$isSortReq_BOO	=	strlen($sortSQL_STR) > 0;
			
		}
		
		//...
		
		$filtersAst_ARR	=	array();
		$isFilterReq_BOO=	$auxMainInst_OBJ->getHandleFilter($filtersAst_ARR, $bookModelInst_OBJ->retAllowFilterColsSet());
		
		//если достаточно данных для фильтрации
		if ($isFilterReq_BOO) {
			
			//print_r($filtersAst_ARR);//!!!//
			
			$quoteColsSet_ARR	=	array(BookModel::BOOK_DEFS_AUTHOR_FULL_NAME, BookModel::BOOK_DEFS_GENRE_TITLE);
			$hashColsSet_ARR	=	array(BookModel::BOOK_DEFS_AUTHOR_FULL_NAME);
			$filterSQL_STR	=	$auxMainInst_OBJ->retFilterSQLByAst($filtersAst_ARR, $quoteColsSet_ARR, $hashColsSet_ARR);
			
		}
		
		$booksList_ARR	=	array(); $booksCount_INT	=	0;
		$whatToSelect_STR	=	BookModel::BOOK_DEFS_ID;
		
		//это первый запрос, нужен чтобы узнать сколько всего книг удовлетворяют фильтрам
		$bookModelInst_OBJ->getBooksListBySortFilterLimit(
			
			$booksList_ARR, $booksCount_INT,
			
			$whatToSelect_STR,
			
			0, 0,
			
			$isSortReq_BOO, $sortSQL_STR,
			$isFilterReq_BOO, $filterSQL_STR
			
		);
		
		if ($requestHandlerInst_OBJ->retIsSuccess()) {
			
			if ($booksCount_INT > 0) {
				
				//print_r($booksList_ARR);//!!!//
				
				$whatToSelect_STR	=	'*';
				$skipCount_INT	=	0;
				$retrCount_INT	=	$booksPerPage_INT;
				$totalCount_INT	=	$booksCount_INT;
				
				$auxMainInst_OBJ->getCalcPagination($skipCount_INT, $pagesNumsSet_ARR, $currPageNum_INT, $booksPerPage_INT, $totalCount_INT);
				
				$booksList_ARR	=	array(); $booksCount_INT	=	0;
				
				//это второй запрос, нужен чтобы получить книги только для текущей страницы
				$bookModelInst_OBJ->getBooksListBySortFilterLimit(
					
					$booksList_ARR, $booksCount_INT,
					
					$whatToSelect_STR,
					
					$skipCount_INT, $retrCount_INT,
					
					$isSortReq_BOO, $sortSQL_STR,
					$isFilterReq_BOO, $filterSQL_STR
					
				);
				
				if ($requestHandlerInst_OBJ->retIsSuccess()) {
					
					$isPrevStepPass_BOO	=	true;
					
				} else { $HTMLContent_HTML	=	$requestHandlerInst_OBJ->retHardAnswer(__LINE__); }
				
			} else { $HTMLContent_HTML	=	$requestHandlerInst_OBJ->retNormalAnswer('Книги не найдены'); }
			
		} else { $HTMLContent_HTML	=	$requestHandlerInst_OBJ->retHardAnswer(__LINE__); }
		
		if ($isPrevStepPass_BOO) {
			
			//$isPrevStepPass_BOO	=	false;
			
			//print_r($booksList_ARR); var_dump($booksCount_INT);
			//print_r($pagesNumsSet_ARR);
			
			$pagesLinksSet_HTML	=	'';
			$addHTTPQuery_STR	=	'';
			
			foreach ($booksList_ARR as $i => $bookData_ARR) {
				
				$bookId_INT	=	$bookData_ARR[BookModel::BOOK_DEFS_ID];
				$bookCover_STR	=	$bookData_ARR[BookModel::BOOK_DEFS_COVER];
				$bookCoverURL_STR	=	'/css/img/book_cover_default.jpg';
				
				if (strlen($bookCover_STR) > 32) {
					
					$bookCoverURL_STR	=	'/uploads/'.$bookCover_STR;
					
				}
				
				$HTMLContent_HTML	.=	view('books_list_book_tile', [
					
					'bookId_INT'	=>	$bookId_INT,
					'bookCoverURL_STR'	=>	$bookCoverURL_STR,
					'booTitle_STR'	=>	$bookData_ARR[BookModel::BOOK_DEFS_TITLE],
					'bookAuthor_STR'=>	$bookData_ARR[BookModel::BOOK_DEFS_AUTHOR_FULL_NAME],
					
				]);
				
				if ($_SERVER['QUERY_STRING'] != '') { $addHTTPQuery_STR	=	'?'.$_SERVER['QUERY_STRING']; }
				
			}
			
			foreach ($pagesNumsSet_ARR as $i => $cycle_pageNum_INT) {
				
				$paginURL_STR	=	'/books/'.$cycle_pageNum_INT.$addHTTPQuery_STR;
				$isPageCurrent_STR	=	$cycle_pageNum_INT == $currPageNum_INT ? ' pagin_button_current' : '';
				
				$pagesLinksSet_HTML	.=	'<div class="pagin_button'.$isPageCurrent_STR.'"><a href="'.$paginURL_STR.'"></a>'.$cycle_pageNum_INT.'</div>';
				
			}
			
			$HTMLContent_HTML	=	$pagesLinksSet_HTML.'<br>'.$HTMLContent_HTML.'<br><br><br><br>'.$pagesLinksSet_HTML;
			
		}
		
		$filtersFormId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
		$sortFormId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
		
		$addFiltersButton_HTML	=	'<div class="minorSmallButton" onclick="App.showOrHideBlockById(\'filters_sorting_form_out\');">Фильтры и Сортировка</div>';
		
$addConfirmAndCloseButtons_HTML	=	'<div onclick="App.onFiltersAndSortFormButtonClick(\''.$filtersFormId_STR.'\', \''.$sortFormId_STR.'\', \'/books\');" class="majorAJAXHTMLButton">Подтвердить выбор</div><br><br><div class="minorSmallButton" onclick="App.showOrHideBlockById(\'filters_sorting_form_out\');">Закрыть</div>';
		
		return $addFiltersButton_HTML.
			'<div id="filters_sorting_form_out" style="display: none;">'.
				'<div class="f_and_s_div">'.$this->retFormFiltersBlock($filtersFormId_STR, $filtersAst_ARR).'</div>'.
				'<div class="f_and_s_div">'.$this->retFormSortBlock($sortFormId_STR, $sortAst_ARR).'</div>'.
				'<br><br>'.$addConfirmAndCloseButtons_HTML.
			'</div>'.
			'<br><br>'.
			$HTMLContent_HTML;
		
	}//retShowPage
	
	public function retFormSortBlock($HTMLFormId_STR, $sortAst_ARR) : view {
		
		$bookModelInst_OBJ	=	BookModel::retInst();
		$auxMainInst_OBJ	=	AuxMain::retInst();
		
		//print_r($sortAst_ARR);//!!!//
		
		$sortFields_HTML	=	'';
		$possibleRulesSet_ARR	=	array('asc', 'desc', 'default');
		
		foreach ($sortAst_ARR as $colName_STR => $rule_STR) {
			
			$HTMLFieldTitle_STR	=	$bookModelInst_OBJ->retFieldTitleByColName($colName_STR);
			$HTMLFieldId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
			$selectOptions_HTML	=	'';
			
			foreach ($possibleRulesSet_ARR as $i => $possibleRule_STR) {
				
				$addIfSelected_STR	=	$rule_STR == $possibleRule_STR ? ' selected="selected"' : '';
				
				if ($possibleRule_STR == 'asc') {
					
					$optionCapt_STR	=	'По возрастанию';
					
				} else {
				if ($possibleRule_STR == 'desc') {
					
					$optionCapt_STR	=	'По убыванию';
					
				} else {
					
					$optionCapt_STR	=	'По умолчанию';
					
				}
				}
				
				$selectOptions_HTML	.=	'<option value="'.$possibleRule_STR.'"'.$addIfSelected_STR.'>'.$optionCapt_STR.'</option>';
				
			}
			
			$sortFields_HTML	.=	view('app_std_select_field',
				[
					'HTMLFieldId_STR'	=>	$HTMLFieldId_STR,
					'HTMLFieldTitle_STR'	=>	$HTMLFieldTitle_STR,
					'selectOptions_HTML'	=>	$selectOptions_HTML,
					'HTMLFieldDescr_HTML'	=>	'Выберите "По умолчанию" чтобы убрать сортировку',
					'HTMLFieldName_STR'	=>	' title="'.$colName_STR.'"',
					
				]
			);
			
		}
		
		return view('books_list_f_and_s_form',
			[
				'HTMLFormId_STR'	=>	$HTMLFormId_STR,
				'formContent_HTML'	=>	$sortFields_HTML,
			]
		);
		
	}//retFormSortBlock
	
	public function retFormFiltersBlock($HTMLFormId_STR, $filtersAst_ARR) : view {
		
		$bookModelInst_OBJ	=	BookModel::retInst();
		$auxMainInst_OBJ	=	AuxMain::retInst();
		
		$filterAssocs_ARR	=	array();
		$bookModelInst_OBJ->getFilterAssocs($filterAssocs_ARR);
		
		//print_r($filterAssocs_ARR);//!!!//
		
		$filters_HTML	=	'';
		
		foreach ($filterAssocs_ARR as $colName_STR => $valAndIdsSet_ARR) {
			
			$HTMLFieldTitle_STR	=	$bookModelInst_OBJ->retFieldTitleByColName($colName_STR);
			$HTMLFieldInputs_HTML	=	'';
			$fieldName_STR	=	$colName_STR;
			
			foreach ($valAndIdsSet_ARR as $value_ANY => $idsSet_ARR) {
				
				$isSelect_INT	=	0;
				
				if (key_exists($colName_STR, $filtersAst_ARR)) {
					
					$isSelect_INT	=	(int) in_array($value_ANY, $filtersAst_ARR[$colName_STR][1]);
					
				}
				
				$HTMLFieldInputs_HTML	.=	'<input class="HTMLInput" type="button" title="'.$isSelect_INT.'" value="'.$value_ANY.'"><div class="HTMLFieldCapt">'.$value_ANY.'</div><br><br>';
				
			}
			
			$HTMLFieldId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
			$filters_HTML	.=	view('app_std_multibox_field', [
            	
				'fieldName_STR'	=>	$fieldName_STR,
				'HTMLFieldId_STR'	=>	$HTMLFieldId_STR,
				'HTMLFieldTitle_STR'=>	$HTMLFieldTitle_STR,
				'HTMLFieldInputs_HTML'	=>	$HTMLFieldInputs_HTML,
				
			]);
			
		}
		
		return view('books_list_f_and_s_form', [
			
			'HTMLFormId_STR'	=>	$HTMLFormId_STR,
			'formContent_HTML'	=>	$filters_HTML
			
		]);
		
	}//retFormFiltersBlock
	
}//BookController

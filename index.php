<?php

/**
 * Simple page for showing several calibre collections
 */

require_once 'libs/Smarty-3.1.14/libs/Smarty.class.php';

$smarty = new Smarty();
$smarty->setTemplateDir('templates');
$smarty->setCompileDir('templates_c');

$COLLECTIONS_ROOT_PATH = 'collections';
$BOOKS_PER_PAGE = 20;

if(isset($COLLECTIONS_ROOT_PATH) == false && empty($COLLECTIONS_ROOT_PATH) == true) {
    die('You must set a path in the $COLLECTIONS_ROOT_PATH variable');
}

if(substr($COLLECTIONS_ROOT_PATH,-1) != '/') {
    $COLLECTIONS_ROOT_PATH.='/';
}

if(file_exists($COLLECTIONS_ROOT_PATH) == false && is_dir($COLLECTIONS_ROOT_PATH) == false) {
    die('The path $COLLECTIONS_ROOT_PATH does not exist or is not a directory');
}


$scanned_directory = array_diff(scandir($COLLECTIONS_ROOT_PATH), array('..', '.'));
$collection_directories = array();
if(count($scanned_directory) > 0) {
    foreach($scanned_directory as $dir_entrance) {
      $file_path = $COLLECTIONS_ROOT_PATH.$dir_entrance;
      if(is_dir($file_path) == true) {
          $collection_directories[] = $dir_entrance;
      }
    }
}

$smarty->assign('COLLECTIONS_DIRS',$collection_directories);

// the user selected a collection and it is in the collection part well than lets do it
if(isset($_GET['collection']) == true && in_array($_GET['collection'],$collection_directories) == true) {
    $collection_path = $COLLECTIONS_ROOT_PATH.$_GET['collection'];
    readCalibreDatabase($smarty,$collection_path,$BOOKS_PER_PAGE);
}

$smarty->display('main.tpl');

/**
 * Reads the calibre collection an displays the result
 * @param Smarty $smarty
 * @param $collection_path
 * @param $books_per_page
 */
function readCalibreDatabase(Smarty &$smarty, $collection_path,$books_per_page) {
    if(file_exists($collection_path) == false && is_dir($collection_path) == false) {
        die('Problem while reading the selected collection');
    }

    // check if the database file exists
    $db_file = $collection_path.'/metadata.db';
    if(file_exists($db_file) == false) {
        die('Problem while reading the selected collection');
    }

    $db = connectDB($db_file);

    if(isset($_GET['page']) == false) {
        $_GET['page'] = 0;
    }

    $booksQuery = queryDB($db,'SELECT * FROM books ORDER BY sort ASC LIMIT '.$_GET['page']*$books_per_page.','.$books_per_page);
    $books = $booksQuery->fetchAll();

    $display_books = array();

    foreach($books as $book) {
        $dataQuery = queryDB($db,'SELECT * FROM data WHERE book = '.$book['id'].' ORDER BY format ASC');
        $data = $dataQuery->fetchAll();

        if(count($data) == 0) {
            continue;
        }

        $display_book = new DisplayBook($book,$data);
        $display_books[] = $display_book;
    }

    $smarty->assign('DISPLAY_BOOKS',$display_books);

    $amount = $db->query('SELECT count(*) FROM books')->fetchColumn();
    $smarty->assign('BOOKS_AMOUNT',$amount);

    // how many nav pages do we have her
    $totalPageAmount = 0;
    if($amount > $books_per_page) {
      $totalPageAmount = floor($amount / $books_per_page);
      if($amount % $books_per_page != 0) {
        $totalPageAmount+=1;
      }
    }

    $smarty->assign('PAGES',$totalPageAmount);
}

/**
 * Connects to a database
 * @param $db_file
 * @return PDO
 */
function connectDB($db_file) {
    try {
        $db = new PDO('sqlite:'.$db_file);
        return $db;
    } catch(PDOException $e) {
        die($e->getMessage());
    }
}

/**
 * @param $db
 * @param $sql
 * @return mixed
 */
function queryDB(&$db,$sql) {
    $qh = $db->prepare($sql);
    if(!$qh) {
        die("query error ($sql)");
    }
    $qh->setFetchMode(PDO::FETCH_ASSOC);
    $qh->execute();
    return $qh;
}

/**
 * Class DisplayBook
 * Simple data holder for displaying a book
 */
class DisplayBook {
    var $book;
    var $data;

    function __construct($book, $data) {
        $this->book = $book;
        $this->data = $data;
    }
}
?>
<?php

require "./search.php";

use FJU\OPAC as OPAC;

$search = OPAC\query(OPAC\TYPE_KEYWORD, "哈利");

// 取得筆數
echo "筆數" . $search->getCount();

// 取得頁數
echo "總頁數" . $search->getPageCount();

// 每一頁的位移值
echo "位移值" . $search->getPageOffset();

// 取得第n頁的清單, array of book_id
var_dump( $search->getPage(8) );

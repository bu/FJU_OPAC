<?php

namespace FJU\OPAC;

const TYPE_KEYWORD = "Y";

function query($query_type, $keyword) {
    return ( new SearchResult() )->setType($query_type)->setKeyword($keyword)->exec();
}

class SearchResult {
    protected $count = 0;

    protected $pages = [
        "count" => 1,
        "offset" => 12
    ];

    protected $type = TYPE_KEYWORD;
    
    // raw text for the keyword ( 哈利 )
    protected $keyword = "";
    // ucs string ( {u54C8}{u5229} )
    protected $keyword_ucs = "";
    // utf-8 encoded ( %E5%93%88%E5%88%A9 )
    protected $keyword_utf8_encoded = "";
    




    public function setType($query_type) {
        $this->type = $query_type;

        return $this;
    }

    public function setKeyword($keyword) {
        $this->keyword = $keyword;

        return $this;
    }

    protected function _getURLContent($url) {
        return file_get_contents($url);
    }

    public function exec() {
        // we fetch for the first page get the results
        $url = "http://140.136.208.1/search~S0*cht/?searchtype=" . $this->type . "&searcharg=" . urlencode($this->keyword);

        $content = $this->_getURLContent($url);

        // ---------------------

        $matches = [];

        $result = preg_match('/<a\ href="\/search\~S0\*cht\/X\?NOSRCH=([{}0-9a-zA-Z]+)&SORT=D&SUBKEY=([A-Z\%0-9]+)">/', $content, $matches);

        if($result > 0) {
            $this->keyword_ucs = $matches[1];
            $this->keyword_utf8_encoded = $matches[2];
        }
        
        // -------------------

        $matches = [];

        $result = preg_match('/關鍵字\ \(([0-9]+)\-([0-9]+) 之 ([0-9]+)\)/', $content, $matches);

        if($result > 0) {
            $this->count = $matches[3];

            $this->pages['count'] = ceil($matches[3] / $matches[2]);
            $this->pages['offset'] = $matches[2];
        }

        return $this;
    }

    public function getCount() {
        return $this->count;
    }

    public function getPageCount() {
        return $this->pages['count'];
    }

    public function getPageOffset() {
        return $this->pages['offset'];
    }

    public function getPage($page) {
        // we fetch for the first page get the results
        $offset = 1 + ($page - 1) * $this->pages["offset"];

        $url = "http://140.136.208.1/search~S0*cht?/Y" . $this->keyword_ucs . "&SORT=D/Y" . $this->keyword_ucs . "&SORT=D&SUBKEY=%E4%B8%AD%E6%96%87/". $offset . "%2C" . $this->count . "%2C" . $this->count . "%2CB/browse";

        $content = $this->_getURLContent($url);

        $matches = [];

        echo "\n\n\n\n"; 
        preg_match_all("/browse\?save=([a-z0-9]+)#anchor_[0-9]+/", $content, $matches);

        return $matches[1];
    }
}

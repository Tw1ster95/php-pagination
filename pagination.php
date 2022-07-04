<?php
class Pagination {
	public function __construct($perPage = 20, $usePrevNextBtns = true)
	{
        $this->currentPage = ($_REQUEST['page'] ?? 1) - 1;
        if($this->currentPage < 0) $this->currentPage = 0;
        $this->perPage = $perPage;
        $this->totalItems = 0;
        $this->usePrevNextBtns = $usePrevNextBtns;
        $this->totalPages = 0;
        $this->nextPage = 0;
        $this->prevPage = 0;
        $this->prevBtnText = "Previous";
        $this->nextBtnText = "Next";

        $pagePos = strpos($_SERVER['REQUEST_URI'], 'page=');
        $pageEndPos = strpos($_SERVER['REQUEST_URI'], '&', $pagePos);
        $uri = '';
        if($pagePos > 0) {
            $uri = substr($_SERVER['REQUEST_URI'], 0, $pagePos);
            if($pageEndPos) $uri .= substr($_SERVER['REQUEST_URI'], $pageEndPos+1, strlen($_SERVER['REQUEST_URI']));
            if($pagePos-1 >= 0 && $_SERVER['REQUEST_URI'][$pagePos-1] !== '&' && $_SERVER['REQUEST_URI'][$pagePos-1] !== '?') $uri .= '&';
        }
        else
            $uri = $_SERVER['REQUEST_URI'] . '?';
        $this->url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$uri";
	}

    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;

        $pages = floor($this->totalItems / $this->perPage);
        $this->totalPages = $pages + ($this->totalItems > ($this->totalItems + ($pages * $this->perPage)) ? 1 : 0);

        $this->prevPage = $this->currentPage <= 1 ? 1 : ($this->currentPage);
        $this->nextPage = $this->currentPage >= ($this->totalPages-1) ? $this->totalPages : ($this->currentPage+2);
    }

    public function setPrevBtnText($text)
    {
        $this->prevBtnText = $text;
    }

    public function setNextBtnText($text)
    {
        $this->nextBtnText = $text;
    }

    public function formatQuery($query)
    {
        $fromPos = strpos($query, 'FROM');
        $from = substr($query, $fromPos);
        $query = substr_replace($query, ", (SELECT COUNT(*) $from) as total_rows ", $fromPos, 0);
        $query .= " LIMIT " . ($this->currentPage * $this->perPage) . ", " . $this->perPage;
        return $query;
    }

    public function getPageButtonsHtml()
    {
        if(!$this->totalPages) return '';
        $html = "<ul class='pagination'>";
        if($this->usePrevNextBtns) $html .= "<li".($this->currentPage <= 0 ? " class='disabled'" : "")."><a href='{$this->url}page={$this->prevPage}'>{$this->prevBtnText}</a></li>";
        for($i = 0; $i < $this->totalPages; $i++) {
            $html .= "<li".($this->currentPage == $i ? " class='current'" : "")."><a href='{$this->url}page=".($i+1)."'>".($i+1)."</a></li>";
        }
        if($this->usePrevNextBtns) $html .= "<li".($this->currentPage >= ($this->totalPages-1) ? " class='disabled'" : "")."><a href='{$this->url}page={$this->nextPage}'>{$this->nextBtnText}</a></li>";
        $html .= "</ul>";
        return $html;
    }
}

<?php

namespace Phact\Pagination;

use App\Core\HttpRequest;
use Phact\Exceptions\InvalidConfigException;
use Phact\Helpers\Configurator;
use Phact\Helpers\SmartProperties;

/**
 * Class Pagination
 *
 * @property $data array
 *
 * @package Phact\Pagination
 */
class Pagination
{
    use SmartProperties;

    /**
     * @var array|PaginableInterface
     */
    protected $_provider;

    protected $_page;

    protected $_pageSize;

    protected $_defaultPage = 1;

    protected $_defaultPageSize = 10;

    protected $_id = 0;

    protected static $_key = 0;

    protected $_dataType;

    protected $_total = null;

    protected $_lastPage = null;

    public $pageKeyTemplate = 'page';

    public $pageSizeKeyTemplate = 'Pagination_Size_{id}';

    public $pageSizes = [10, 20, 50];

    /** @var HttpRequest */
    public $request;
    
    public function __construct($provider, $options = [])
    {
        if (!($provider instanceof PaginableInterface) && !(is_array($provider))) {
            throw new InvalidConfigException("Pagination \$provider must be instance of an array or PaginableInterface");
        }
        self::$_key++;
        $this->_id = self::$_key;
        $this->_provider = $provider;
        Configurator::configure($this, $options);
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getPage()
    {
        return $this->_page;
    }

    public function setPage($page)
    {
        $this->_page = $page;
    }

    public function getPageSize()
    {
        return $this->_pageSize;
    }

    public function setPageSize($pageSize)
    {
        $this->_pageSize = $pageSize;
    }

    public function getDefaultPage()
    {
        return $this->_defaultPage;
    }

    public function setDefaultPage($page)
    {
        $this->_defaultPage = $page;
    }

    public function getDefaultPageSize()
    {
        return $this->_defaultPageSize;
    }

    public function setDefaultPageSize($pageSize)
    {
        $this->_defaultPageSize = $pageSize;
    }

    public function getDataType()
    {
        return $this->_dataType;
    }

    public function setDataType($page)
    {
        $this->_dataType = $page;
    }

    public function fetchPage()
    {
        if (!$this->getPage()) {
            $page = $this->getRequestPage();
            if (!$page) {
                $page = $this->getDefaultPage();
            }
            if ($page <= 0) {
                $page = 1;
            } elseif ($page > $this->getLastPage()) {
                $page = $this->getLastPage();
            }
            $this->setPage($page);
        }
        return $this->getPage();
    }

    public function fetchPageSize()
    {
        if (!$this->getPageSize()) {
            $pageSize = $this->getRequestPageSize();
            if (!$pageSize) {
                $pageSize = $this->getDefaultPageSize();
            }
            $this->setPageSize($pageSize);
        }
        return $this->getPageSize();
    }

    public function getRequestPage()
    {
        $key = $this->getRequestPageKey();
        return $this->getRequestValue($key);
    }

    public function getRequestPageSize()
    {
        $key = $this->getRequestPageSizeKey();
        return $this->getRequestValue($key);
    }

    public function getRequestPageKey()
    {
        return $this->buildRequestKey($this->pageKeyTemplate);
    }

    public function getRequestPageSizeKey()
    {
        return $this->buildRequestKey($this->pageSizeKeyTemplate);
    }

    public function buildRequestKey($template)
    {
        return strtr($template, [
            '{id}' => $this->getId()
        ]);
    }

    public function getRequestValue($key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function getFirstPage()
    {
        return 1;
    }

    public function getFirstPageUrl()
    {
        return $this->getUrl($this->getFirstPage());
    }

    /**
     * @return false|float|int
     */
    public function getLastPage()
    {
        if (is_null($this->_lastPage)) {
            $pageSize = $this->fetchPageSize();
            $total = $this->getTotal();
            $result = ceil($total / $pageSize);
            $this->_lastPage = $result >= 1 ? $result : 1;
        }
        return $this->_lastPage;
    }

    public function getLastPageUrl()
    {
        return $this->getUrl($this->getLastPage());
    }

    public function getPreviousPage()
    {
        $page = $this->fetchPage() - 1;
        if ($this->hasPage($page)) {
            return $page;
        }
        return null;
    }

    public function hasPreviousPage()
    {
        return (bool) $this->getPreviousPage();
    }

    public function getPreviousPageUrl()
    {
        return $this->hasPreviousPage() ? $this->getUrl($this->getPreviousPage()) : null;
    }

    public function getNextPage()
    {
        $page = $this->fetchPage() + 1;
        if ($this->hasPage($page)) {
            return $page;
        }
        return null;
    }

    public function hasNextPage()
    {
        return (bool) $this->getNextPage();
    }

    public function getNextPageUrl()
    {
        return $this->hasNextPage() ? $this->getUrl($this->getNextPage()) : null;
    }

    public function hasPage($page)
    {
        $lastPage = $this->getLastPage();
        return $page >= 1 && $page <= $lastPage;
    }

    /**
     * @param $page
     * @return string
     * @throws InvalidConfigException
     */
    public function getUrl($page)
    {
        $query = $this->request->getQueryArray();
        $query[$this->getRequestPageKey()] = $page;
        return $this->request->getPath() . '?' . http_build_query($query);
    }

    public function getTotal()
    {
        if (is_null($this->_total)) {
            if (is_array($this->_provider)) {
                $this->_total = count($this->_provider);
            } elseif ($this->_provider instanceof PaginableInterface) {
                $this->_total = $this->_provider->getPaginationTotal();
            } else {
                $this->_total = null;
            }
        }
        return $this->_total;
    }

    public function getData()
    {
        $page = $this->fetchPage();
        $pageSize = $this->fetchPageSize();

        $limit = $pageSize;
        $offset = ($page - 1) * $pageSize;

        if (is_array($this->_provider)) {
            return array_slice($this->_provider, $offset, $limit);
        }

        if ($this->_provider instanceof PaginableInterface) {
            return $this->_provider->setPaginationLimit($limit)
                ->setPaginationOffset($offset)
                ->getPaginationData($this->getDataType());
        }

        return [];
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getLinkHeader(): string
    {
        $header = [];
        $hostInfo = $this->request->getHostInfo();
        $firstPage = (int)$this->getFirstPage();
        $lastPage = (int)$this->getLastPage();

        if ($firstPage !== $lastPage) {
            $page = (int)$this->fetchPage();
            $prevPage = (int)$this->getPreviousPage();
            $nextPage = (int)$this->getNextPage();

            if ($firstPage !== $page) {
                $header[] = sprintf('<%s%s>; rel="first"', $hostInfo, $this->getUrl($firstPage));
            }

            if ($prevPage) {
                $header[] = sprintf('<%s%s>; rel="prev"', $hostInfo, $this->getUrl($prevPage));
            }

            if ($nextPage) {
                $header[] = sprintf('<%s%s>; rel="next"', $hostInfo, $this->getUrl($nextPage));
            }
            if ($lastPage && $lastPage !== $page) {
                $header[] = sprintf('<%s%s>; rel="last"', $hostInfo, $this->getUrl($lastPage));
            }

            $header = 'Link: ' . implode(', ', $header);
        }

        return $header === [] ? '' : $header;
    }
}
<?php
/**
 * BEAR.Framework
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Framework\Module\Database;

use Pagerfanta\Pagerfanta;

use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Pagerfanta\View\ViewInterface;
use Pagerfanta\View\TwitterBootstrapView;
/**
 * Paging Query
 *
 * @package    BEAR.Framework
 * @subpackage Database
 */
class Pager
{
    private $maxPerPage = 2;
    private $pageKey = '_start';
    private $pager = [];
    private $currentPage;
    private $viewOptions = [
    'prev_message' => '&laquo;',
    'next_message' => '&raquo;'
    ];
    private $view;
    private $routeGenerator;

    /**
     * Total number
     *
     * @var int
     */
    private $count;

    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;

        return $this;
    }

    public function setPageKey($pageKey)
    {
        $this->pageKey = $pageKey;

        return $this;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function setView(ViewInterface $view)
    {
        $this->view = $view;

        return $this;
    }

    public function setRouteGenerator(Callable $routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;

        return $this;
    }

    public function setViewOption($key, $value)
    {
        $this->viewOptions[$key] = $value;

        return $this;
    }

    /**
     * Constructor
     *
     * @param Pagerfanta $pagerfanta
     */
    public function __construct(DriverConnection $db, Pagerfanta $pagerfanta)
    {
        $this->db = $db;

        $currentPage = $this->currentPage ?: (isset($_GET[$this->pageKey]) ? $_GET[$this->pageKey] : 1);
        $this->firstResult = ($currentPage - 1) * $this->maxPerPage;
        $pagerfanta->setMaxPerPage($this->maxPerPage);
        $pagerfanta->setCurrentPage($currentPage, false, true);
        //view
        $this->pager = [
            'maxPerPage' => $this->maxPerPage,
            'current' => $currentPage,
            'total' => $pagerfanta->getNbResults(),
            'hasNext' => $pagerfanta->hasNextPage(),
            'hasPrevious' => $pagerfanta->hasPreviousPage(),
            'html' => $this->getHtml($pagerfanta)
        ];
    }

    public function getPagerQuery($query)
    {
        $pagerQuery = $this->db->getDatabasePlatform()->modifyLimitQuery($query, $this->maxPerPage, $this->firstResult);

        return $pagerQuery;
    }

    private function getHtml(Pagerfanta $pagerfanta)
    {
        $view = $this->view ?: new TwitterBootstrapView;
        $routeGenerator = $this->routeGenerator ?: function($page) {
            return "?{$this->pageKey}={$page}";
        };
        $html = $view->render(
            $pagerfanta,
            $routeGenerator,
            $this->viewOptions
        );

        return $html;
    }
}

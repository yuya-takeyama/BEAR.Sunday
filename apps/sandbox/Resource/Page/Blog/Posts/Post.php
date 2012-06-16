<?php
namespace sandbox\Resource\Page\Blog\Posts;

use BEAR\Resource\Client as Resource;
use BEAR\Resource\Annotation\Provides;

use BEAR\Framework\Resource\AbstractPage as Page;
use BEAR\Framework\Inject\ResourceInject;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

/**
 * BLog post page
 * 
 * @package    sandbox
 * @subpackage page
 */
class Post extends Page
{
	use ResourceInject;
	
	/**
	 * Contents
	 * 
	 * @var array
	 */
    public $body = [
        'post' => [
            'title' => '',
            'body' => ''
        ],
    ];

    /**
     * Get
     * 
     * @param int $id
     * 
     * @Cache(5)
     */
    public function onGet($id)
    {
        $this['post'] = $this->resource->get->uri('app://self/blog/posts')->withQuery(['id' => $id])->eager->request()->body;
        return $this;
    }
}

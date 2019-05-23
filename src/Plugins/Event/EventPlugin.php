<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/4/18
 * Time: 13:52
 */

namespace ESD\Core\Plugins\Event;

use ESD\Core\Exception;
use ESD\Core\PlugIn\AbstractPlugin;
use ESD\Core\Plugins\DI\DIPlugin;
use ESD\Core\Server\Process\Message\MessageProcessor;
use ESD\Core\Server\Server;
use ESD\Coroutine\Context\Context;

/**
 * Event 插件加载器
 * Class EventPlug
 * @package ESD\BaseServer\Plugins\Event
 */
class EventPlugin extends AbstractPlugin
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct()
    {
        parent::__construct();
        $this->atAfter(DIPlugin::class);
    }

    /**
     * 在服务启动前
     * @param Context $context
     * @throws \Exception
     */
    public function beforeServerStart(Context $context)
    {
        //创建事件派发器
        $this->eventDispatcher = new EventDispatcher(Server::$instance);
        Server::$instance->setEventDispatcher($this->eventDispatcher);
        $context->add("eventDispatcher", $this->eventDispatcher);
    }

    /**
     * 在进程启动前
     * @param Context $context
     * @throws Exception
     */
    public function beforeProcessStart(Context $context)
    {
        //创建事件派发器
        $this->eventDispatcher = Server::$instance->getEventDispatcher();
        //注册事件派发处理函数
        MessageProcessor::addMessageProcessor(new EventMessageProcessor($this->eventDispatcher));
        //ready
        $this->ready();
    }

    /**
     * 获取插件名字
     * @return string
     */
    public function getName(): string
    {
        return "Event";
    }
}
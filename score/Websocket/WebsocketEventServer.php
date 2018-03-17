<?php
namespace Swoolefy\Websocket;

use Swoolefy\Core\Swfy;
use Swoolefy\Core\WebsocketEventInterface;
use Swoolefy\Websocket\WebsocketServer;

// 如果直接通过php RpcServer.php启动时，必须include的vendor/autoload.php
if(isset($argv) && $argv[0] == basename(__FILE__)) {
	include_once '../../vendor/autoload.php';
}

class WebsocketEventServer extends WebsocketServer implements WebsocketEventInterface {
	/**
	 * __construct 初始化
	 * @param array $config
	 */
	public function __construct(array $config=[]) {
		// 获取当前服务文件配置
		$config = array_merge(
				include(__DIR__.'/config.php'),
				$config
			);
		parent::__construct($config);
		// 设置当前的服务名称
	}

	/**
	 * onWorkerStart worker启动函数处理
	 * @param    object  $server
	 * @param    int    $worker_id
	 * @return   void
	 */
	public function onWorkerStart($server, $worker_id) {}

	/**
	 * onOpen 
	 * @param    object  $server
	 * @param    object  $request
	 * @return   void
	 */
	public function onOpen($server, $request) {}

	/**
	 * onRequest 接受http请求处理
	 * @param    object  $request
	 * @param    object  $response
	 * @return   void
	 */
	public function onRequest($request, $response) {
		swoole_unpack(self::$App)->run($request, $response);
	}

	/**
	 * onMessage 接受信息并处理信息
	 * @param    object  $server
	 * @param    object  $frame
	 * @return   void
	 */
	public function onMessage($server, $frame) {
		$fd = $frame->fd;
		$data = $frame->data;
		$opcode = $frame->opcode;
		$finish = $frame->finish;
		// 数据接收是否完整
		if($finish) {
			// utf-8文本数据
			if($opcode == WEBSOCKET_OPCODE_TEXT) {
				swoole_unpack(self::$service)->run($fd, $data);
			}else if($opcode == WEBSOCKET_OPCODE_BINARY) {
				// 二进制数据
				var_dump($data);
			}
			
		}else {
			// 断开连接
			
		}
		
	}

	/**
	 * onTask 异步任务处理
	 * @param    object  $server
	 * @param    int     $task_id
	 * @param    int     $from_worker_id
	 * @param    mixed   $data
	 * @return   void
	 */
	public function onTask($server, $task_id, $from_worker_id, $data) {
		list($callable, $taskData, $fd) = $data;		
		swoole_unpack(self::$service)->run($fd, [$callable, $taskData]);
		return ;
	}

	/**
	 * onFinish 任务完成
	 * @param    object  $server
	 * @param    int     $task_id
	 * @param    mixed   $data
	 * @return   void
	 */
	public function onFinish($server, $task_id, $data) {}

	/**
	 * onClose 连接断开处理
	 * @param    object  $server
	 * @param    int     $fd
	 * @return   void
	 */
	public function onClose($server, $fd) {}

}

if(isset($argv) && $argv[0] == basename(__FILE__)) {
	$websocketserver = new WebsocketEventServer();
	$websocketserver->start();
}
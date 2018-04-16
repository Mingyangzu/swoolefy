<?php
/**
+----------------------------------------------------------------------
| swoolfy framework bases on swoole extension development
+----------------------------------------------------------------------
| Licensed ( https://opensource.org/licenses/MIT )
+----------------------------------------------------------------------
| Author: bingcool <bingcoolhuang@gmail.com || 2437667702@qq.com>
+----------------------------------------------------------------------
*/

namespace Swoolefy\Core\Task;

use Swoolefy\Core\BaseServer;
use Swoolefy\Core\Task\AsyncTask;
use Swoolefy\Core\Task\AppAsyncTask;

class TaskManager {
	
	use \Swoolefy\Core\SingleTrait;

	/**
	 * $is_http_protocol_service 是否是http服务 
	 * @var boolean
	 */
	protected static $is_http_protocol_service = false;

	/**
	 * asyncTask 异步任务投递
	 * @param  mixed  $callable
	 * @param  mixed  $data
	 * @return mixed
	 */
	public static function asyncTask($callable, $data = []) {
		if(self::$is_http_protocol_service) {
			return AppAsyncTask::registerTask($callable, $data);
		}

		if(BaseServer::getServiceProtocol() == SWOOLEFY_HTTP) {
			self::$is_http_protocol_service = true;
			return AppAsyncTask::registerTask($callable, $data);
		}else {
			return AsyncTask::registerTask($callable, $data);
		}
	}
}
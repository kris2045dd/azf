<?php

namespace App\Task;

class Delete30DaysAgoLogFiles extends TaskBase
{

	public function run()
	{
		/* exec() has been disabled for security reasons
		$log_dir = storage_path('logs/vendor');
		$cmd = "find {$log_dir} -type f -mtime +30 -name '*.log' -delete";
		exec($cmd, $output, $return_var);
		*/


		$days_ago = strtotime('-30 days');

		$log_dir = storage_path('logs/vendor');
		foreach (glob("{$log_dir}/*/*.log") as $file) {
			$last_modified_time = filemtime($file);
			if ($last_modified_time < $days_ago) {
				unlink($file);
			}
		}
	}

}

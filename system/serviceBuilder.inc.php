<?php
class serviceBuilder {

	public $id, $core;

	public function __construct($core) {
		$this->core = $core;
		$this->serviceBuilder($this->core->action);
	}

	public function serviceBuilder($service) {
		$this->core->logEvent("Starting service builder for page: " . $this->core->page . " action: " . $this->core->action, "3");

		if (!isset($this->core->role)) {
			/*
			 * User is not authenticated
			 * Non-autoloading views available without authorization are listed here.
			 */

			if (empty($service)) {
				$this->core->throwError("No service selected");
			} elseif ($service) {
				$this->initService($service);
			}

		} else {
			/*
			 * User is authenticated
			 * Non-autoloading views available with authorization are listed here.
			 */

			if (empty($service)) {
				$this->core->throwError("No service selected");
			} elseif ($service) {
				$this->initService($service);
			}
		}
	}

	public function initService($service) {
		$serviceInclude = $this->core->conf['conf']['servicePath'] . $service . ".inc.php";

		if (file_exists($serviceInclude)) {
			$this->core->logEvent("Initializing service $service", "3");

			require_once $serviceInclude;

			$this->service = new $service();
			$serviceConfig = $this->service->configService();
		} else {
			$this->core->throwError("Required service missing $serviceInclude");
		}

		if ($serviceConfig->output == TRUE) {
			$this->service->runService($this->core);
		}
	}
}

?>
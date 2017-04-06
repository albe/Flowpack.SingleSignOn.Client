<?php
namespace Flowpack\SingleSignOn\Client;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * Connect SSO specific signals
 */
class Package extends \Neos\Flow\Package\Package {

	/**
	 * @param \Neos\Flow\Core\Bootstrap $bootstrap
	 * @return void
	 */
	public function boot(\Neos\Flow\Core\Bootstrap $bootstrap) {
		$bootstrap->getSignalSlotDispatcher()->connect(
			'Neos\Flow\Security\Authentication\AuthenticationProviderManager',
			'loggedOut',
			'Flowpack\SingleSignOn\Client\Service\SingleSignOnManager',
			'logout'
		);
	}
}


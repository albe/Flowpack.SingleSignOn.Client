<?php
namespace Flowpack\SingleSignOn\Client\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Flowpack\SingleSignOn\Client\Domain\Model\SsoClient;

/**
 * Interface for a mapper service that maps a global account to a local account
 */
interface GlobalAccountMapperInterface {

	/**
	 * @param \Flowpack\SingleSignOn\Client\Domain\Model\SsoClient $ssoClient
	 * @param array $globalAccountData
	 * @return \Neos\Flow\Security\Account
	 */
	public function getAccount(SsoClient $ssoClient, array $globalAccountData);

}

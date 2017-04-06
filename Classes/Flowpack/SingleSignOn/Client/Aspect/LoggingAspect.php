<?php
namespace Flowpack\SingleSignOn\Client\Aspect;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Client". *
 *                                                                               */

use Neos\Flow\Annotations as Flow;

/**
 * An aspect which logs SSO relevant actions
 *
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class LoggingAspect {

	/**
	 * @var \Neos\Flow\Log\SecurityLoggerInterface
	 * @Flow\Inject
	 */
	protected $securityLogger;

	/**
	 * Log signed request pattern failures
	 *
	 * @Flow\AfterReturning("setting(Flowpack.SingleSignOn.Client.log.logFailedSignedRequests) && method(Flowpack\SingleSignOn\Client\Security\RequestPattern\SignedRequestPattern->emitSignatureNotVerified())")
	 * @param \Neos\Flow\Aop\JoinPointInterface $joinPoint The current joinpoint
	 */
	public function logSignedRequestPatternFailures(\Neos\Flow\Aop\JoinPointInterface $joinPoint) {
		$request = $joinPoint->getMethodArgument('request');
		if ($request instanceof \Neos\Flow\Mvc\RequestInterface) {
			if ($request->getControllerObjectName() === 'Flowpack\SingleSignOn\Client\Controller\SessionController') {
				$this->securityLogger->log('Signature for call to Session service could not be verified', LOG_NOTICE, array(
					'identifier' => $joinPoint->getMethodArgument('identifier'),
					'publicKeyFingerprint' => $joinPoint->getMethodArgument('publicKeyFingerprint'),
					'signature' => base64_encode($joinPoint->getMethodArgument('signature')),
					'signData' => $joinPoint->getMethodArgument('signData'),
					'content' => $joinPoint->getMethodArgument('request')->getHttpRequest()->getContent(),
				));
			}
		}
	}

}

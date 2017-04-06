<?php
namespace Flowpack\SingleSignOn\Client\Security\RequestPattern;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * FIXME This is rather a "UnsignedRequest" pattern because it doesn't match correctly signed requests.
 */
class SignedRequestPattern implements \Neos\Flow\Security\RequestPatternInterface {

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\ObjectManagement\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Client\Security\RequestSigner
	 */
	protected $requestSigner;

	/**
	 * @var \Flowpack\SingleSignOn\Client\Security\PublicKeyResolverInterface
	 */
	protected $publicKeyResolver;

	/**
	 * @var array
	 */
	protected $patternConfiguration;

	/**
	 * Returns the pattern configuration
	 *
	 * @return string The pattern configuration
	 */
	public function getPattern() {
		return $this->patternConfiguration;
	}

	/**
	 * Sets the pattern (match) configuration
	 *
	 * @param object $patternConfiguration The pattern (match) configuration
	 * @return void
	 */
	public function setPattern($patternConfiguration) {
		$this->patternConfiguration = $patternConfiguration;
		if (isset($patternConfiguration['resolverType'])) {
			$this->publicKeyResolver = $this->objectManager->get($patternConfiguration['resolverType']);
		}
	}

	/**
	 * Matches the current request for an unverified signed request.
	 *
	 * This pattern will return TRUE if the request is not signed or
	 * the signature of the request is invalid.
	 *
	 * @param \Neos\Flow\Mvc\RequestInterface $request The request that should be matched
	 * @return boolean TRUE if the pattern matched, FALSE otherwise
	 */
	public function matchRequest(\Neos\Flow\Mvc\RequestInterface $request) {
		/** @var \Neos\Flow\Http\Request $httpRequest */
		$httpRequest = $request->getHttpRequest();
		if ($httpRequest->hasHeader('X-Request-Signature')) {
			$identifierAndSignature = explode(':', $httpRequest->getHeader('X-Request-Signature'), 2);
			if (count($identifierAndSignature) !== 2) {
				throw new \Neos\Flow\Exception('Invalid signature header format, expected "identifier:base64(signature)"', 1354287886);
			}
			$identifier = $identifierAndSignature[0];
			$signature = base64_decode($identifierAndSignature[1]);

			$signData = $this->requestSigner->getSignatureContent($httpRequest);

			$publicKeyFingerprint = $this->publicKeyResolver->resolveFingerprintByIdentifier($identifier);
			if ($publicKeyFingerprint === NULL) {
				throw new \Neos\Flow\Exception('Cannot resolve identifier "' . $identifier .  '"', 1354288898);
			}

			if ($this->rsaWalletService->verifySignature($signData, $signature, $publicKeyFingerprint)) {
				return FALSE;
			} else {
				$this->emitSignatureNotVerified($request, $identifier, $signData, $signature, $publicKeyFingerprint);
			}
		} else {
			$this->emitSignatureHeaderMissing($request);
		}

		return TRUE;
	}

	/**
	 * @param \Neos\Flow\Mvc\RequestInterface $request
	 * @param string $identifier
	 * @param string $signData
	 * @param string $signature
	 * @param string $publicKeyFingerprint
	 */
	protected function emitSignatureNotVerified($request, $identifier, $signData, $signature, $publicKeyFingerprint) {
	}

	/**
	 * @param \Neos\Flow\Mvc\RequestInterface $request
	 */
	protected function emitSignatureHeaderMissing($request) {
	}
}

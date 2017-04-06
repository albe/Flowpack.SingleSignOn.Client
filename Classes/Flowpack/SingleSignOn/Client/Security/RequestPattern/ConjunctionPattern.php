<?php
namespace Flowpack\SingleSignOn\Client\Security\RequestPattern;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * Match subpatterns as a conjunction (match iff all subpatterns match)
 */
class ConjunctionPattern implements \Neos\Flow\Security\RequestPatternInterface {

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Security\RequestPatternResolver
	 */
	protected $requestPatternResolver;

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\ObjectManagement\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $patternValue;

	/**
	 * A list of sub patterns that are matched before this pattern
	 * @var array
	 */
	protected $subPatterns = array();

	/**
	 * Returns the set pattern
	 *
	 * @return string The set pattern
	 */
	public function getPattern() {
		return $this->patternValue;
	}

	/**
	 * Sets the pattern (match) configuration
	 *
	 * @param object $pattern The pattern (match) configuration
	 * @return void
	 */
	public function setPattern($patternValue) {
		$this->patternValue = $patternValue;
		if (isset($patternValue['patterns'])) {
			foreach ($patternValue['patterns'] as $patternConfiguration) {
				$requestPattern = $this->objectManager->get($this->requestPatternResolver->resolveRequestPatternClass($patternConfiguration['patternType']));
				$requestPattern->setPattern($patternConfiguration['patternValue']);
				$this->subPatterns[] = $requestPattern;
			}
		}
	}

	/**
	 * Matches a \Neos\Flow\Mvc\RequestInterface against its set pattern rules
	 *
	 * @param \Neos\Flow\Mvc\RequestInterface $request The request that should be matched
	 * @return boolean TRUE if the pattern matched, FALSE otherwise
	 */
	public function matchRequest(\Neos\Flow\Mvc\RequestInterface $request) {
		/** @var \Neos\Flow\Security\RequestPatternInterface $pattern */
		foreach ($this->subPatterns as $pattern) {
			if ($pattern->matchRequest($request) === FALSE) {
				return FALSE;
			}
		}

		return TRUE;
	}

}

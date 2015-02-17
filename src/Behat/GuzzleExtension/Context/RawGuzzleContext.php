<?php
/**
 * Behat Guzzle Extension
 *
 * PHP version 5
 *
 * @package Behat\GuzzleExtension
 * @author  Dave Nash <dave.nash@teaandcode.com>
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version GIT: $Id$
 * @link    https://github.com/teaandcode/behat-guzzle-extension GuzzleExtension
 */

namespace Behat\GuzzleExtension\Context;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\Response;
use Guzzle\Service\Client;

/**
 * Raw Guzzle context for Behat BDD tool
 * Provides raw Guzzle integration (without step definitions) and web assertions
 *
 * @package Behat\GuzzleExtension\Context
 * @author  Dave Nash <dave.nash@teaandcode.com>
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version Release: @package_version@
 * @link    https://github.com/teaandcode/behat-guzzle-extension GuzzleExtension
 */
class RawGuzzleContext implements GuzzleAwareContext
{
    /**
     * @var Client
     *
     * @access private
     */
    private $client;

    /**
     * @var array
     *
     * @access private
     */
    private $parameters;

    /**
     * @var Response
     *
     * @access private
     */
    private $response;

    /**
     * @var array
     *
     * @access private
     */
    private $result;

    /**
     * Execute command
     *
     * @param string $command Command to execute
     * @param array  $data    Data to send
     *
     * @access protected
     * @return void
     */
    public function executeCommand($command, array $data = array())
    {
        $command = $this->getGuzzleClient()->getCommand($command, $data);

        try {
            $result = $this->getGuzzleClient()->execute($command);
        } catch (ClientErrorResponseException $e) {
            $this->response = $e->getResponse();

            return;
        }

        if (!is_array($result)) {
            $result = array($result);
        }

        $this->response = $command->getResponse();
        $this->result   = $result;
    }

    /**
     * Returns Client instance
     *
     * @access public
     * @return Client
     */
    public function getGuzzleClient()
    {
        if ($this->client === null) {
            throw new \RuntimeException(
                'Guzzle client instance has not been set on Guzzle context ' .
                'class.' . chr(10) . 'Have you enabled the Guzzle Extension?'
            );
        }

        return $this->client;
    }

    /**
     * Sets Client instance
     *
     * @param Client $client Guzzle client
     *
     * @access public
     * @return void
     */
    public function setGuzzleClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Add Guzzle header
     *
     * @param string $field Field name
     * @param string $value Header value
     *
     * @access public
     * @return void
     */
    public function addGuzzleHeader($field, $value)
    {
        $config  = $this->getGuzzleClient()->getConfig();
        $options = $config->get(Client::REQUEST_OPTIONS);

        if (!isset($options['headers'])) {
            $options['headers'] = array();
        }

        $options['headers'][$field] = $value;

        $config->set(Client::REQUEST_OPTIONS, $options);
        $this->getGuzzleClient()->setConfig($config);
    }

    /**
     * Remove Guzzle header
     *
     * @param string $field Field name
     *
     * @access public
     * @return void
     */
    public function removeGuzzleHeader($field)
    {
        $config  = $this->getGuzzleClient()->getConfig();
        $options = $config->get(Client::REQUEST_OPTIONS);

        if (isset($options['headers'][$field])) {
            unset($options['headers'][$field]);
        }

        $config->set(Client::REQUEST_OPTIONS, $options);
        $this->getGuzzleClient()->setConfig($config);
    }

    /**
     * Returns specific Guzzle parameter
     *
     * @param string $name
     *
     * @access public
     * @return mixed
     */
    public function getGuzzleParameter($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
    }

    /**
     * Applies the given parameter to the Guzzle configuration. Consider that
     * all parameters get reset for each feature context
     *
     * @param string $name  The key of the parameter
     * @param string $value The value of the parameter
     *
     * @access public
     * @return void
     */
    public function setGuzzleParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Returns the parameters provided for Guzzle
     *
     * @access public
     * @return array
     */
    public function getGuzzleParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets parameters provided for Guzzle
     *
     * @param array $parameters
     *
     * @access public
     * @return void
     */
    public function setGuzzleParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns Response instance
     *
     * @access public
     * @return Response
     */
    public function getGuzzleResponse()
    {
        return $this->response;
    }

    /**
     * Returns result array
     *
     * @access public
     * @return array
     */
    public function getGuzzleResult()
    {
        return $this->result;
    }
}

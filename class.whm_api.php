<?php
/**
 * Class for executing calls to WHM API, use cPanel API2 functions for the calls
 */
class WHM_API
{
	private $host = '';
	private $user = '';
	private $pass = '';
	private $hash = '';
	private $cpanel_user = '';
	private $headers = array();

	/**
	* Constructor
	* @param string  $ip_address   WHM IP address
	* @param string  $user         WHM login username, usually 'root' with full access permissions
	* @param string  $hash         WHM password or a hash string from WHM >> Clusters >> Remote Access Key
	* @param string  $cpanel_user  cPanel login username, enter if using cPanel API2 functions
	* @param boolean $hash_is_pass set this to true to use login password instead of hash, password is not recommended
	* @param boolean $SSL          if your whm is on SSL ( https ) true, else ( http ) false, http is not recommended
	* @return true
	*/
	public function __construct( $ip_address , $user , $hash , $cpanel_user = '', $hash_is_pass = false, $SSL = true )
	{
		$host = ( $SSL ) ? ( 'https://' . $ip_address . ':2087' ) : ( 'http://' . $ip_address . ':2086' );
		$this->host = $host;
		$this->user = $user;
		$this->cpanel_user = $cpanel_user;

		if ( $hash_is_pass  ) {
			$this->pass = $hash;
			$this->headers[] = 'Authorization: Basic ' . base64_encode( $this->user . ':' . $this->pass );
		} else {
			$this->hash = $hash;
			$this->headers[] = 'Authorization: WHM ' . $this->user . ':' . preg_replace("'(\r|\n|\s|\t)'", '', $this->hash);
		}
	}

	/**
	* send_GET_request to WHM API
	* @param array $params
	* @return mixed
	*/
	private function send_GET_request( $action, $params = array() )
	{
		$curl = curl_init();
		$url = $this->host . '/json-api/' . $action . '?' . http_build_query($params);
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt( $curl, CURLOPT_URL, $url );

		$result = json_decode(curl_exec($curl) , true);

		if ( curl_errno($curl) ) {
			throw new Exception('Error Processing Request: ' . curl_error($curl), 1);
		}
		curl_close($curl);

		if ( isset($result['cpanelresult']['error']) ) {
			throw new Exception('Error Processing Request: ' . $result['cpanelresult']['error'], 1);
		} else if ( isset($result['error']) ) {
			throw new Exception('Error Processing Request: ' . $result['error'], 1);
		}

		return $result;
	}

	/**
	 * Use cPanel API2 functions for the calls
	 *
	 * @param string $module   cPanel API2 module
	 * @param string $function cPanel API2 function
	 * @param array  $params
	 * @return mixed
	 */
	public function cpanel_api2( $module, $function, $params = array() )
	{
		$params = array_merge($params, [
			'cpanel_jsonapi_version' => 2,
			'cpanel_jsonapi_module'  => $module,
			'cpanel_jsonapi_func'    => $function,
			'cpanel_jsonapi_user'    => $this->cpanel_user,
			]);

		$response = $this->send_GET_request('cpanel', $params);
		return $response;
	}
}
// eof
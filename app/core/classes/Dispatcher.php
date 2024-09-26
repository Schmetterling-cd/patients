<?php

namespace app\core\classes;

final class Dispatcher
{

	private string $_templatesPath;
	private string $_cssPath;
	private string $_jsPath;

	private const APP_CONTROLLERS_PATH = '/app/controllers/';

	public function __construct()
	{

		$this->_templatesPath = $_SERVER['DOCUMENT_ROOT'] . '/' . env('TEMPLATES_PATH', 'resources/templates/');
		$this->_cssPath = $_SERVER['DOCUMENT_ROOT'] . '/' . env('TEMPLATES_PATH', 'resources/styles/');
		$this->_jsPath = $_SERVER['DOCUMENT_ROOT'] . '/' . env('TEMPLATES_PATH', 'resources/scripts/');

	}

	public function process()
	{

		if ($this->isGet() && !$this->isApiRequest()) {
			header("Content-Type: text/html; charset=utf-8");
			exit($this->getPage());
		}

		exit($this->callApi());

	}

	public function isGet(): bool
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}

	public function isPost(): bool
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	public function isApiRequest()
	{
		return str_contains($_SERVER['REQUEST_URI'], '/api/');
	}

	private function getPage()
	{

		$routes = include 'routes/pages.php';

		if (!isset($routes[$_SERVER['REQUEST_URI']])) {
			return '404';
		}

		$route = $routes[$_SERVER['REQUEST_URI']];

		return $this->render($route['page'], $route['css'], $route['js'], $route['title']);

	}

	private function render(string $page, array $cssPaths, array $jsPaths, string $title)
	{

		$page = file_get_contents($this->_templatesPath . $page);

		$styles = '';
		if (!empty($cssPaths)) {
			foreach ($cssPaths as $css) {
				$styles .= file_get_contents($this->_cssPath . $css) . "\n";
			}
		}

		$scripts = '';
		if (!empty($jsPaths)) {
			foreach ($jsPaths as $js) {
				$scripts .= file_get_contents($this->_jsPath . $js) . "\n";
			}
		}

		$template = file_get_contents($this->_templatesPath . '/index.html');

		return str_replace(['{{STYLE}}', '{{SCRIPT}}', '{{BODY}}', '{{TITLE}}'], [$styles, $scripts, $page, $title], $template);

	}

	private function callApi()
	{

		if ($this->isGet()) {
			$this->prepareApiGetData();
		}

		$routes = include 'routes/api.php';

		if (!isset($routes[$_SERVER['REQUEST_URI']])) {
			return json_encode([
				'status' => 'ER',
				'message' => 'Сервис не найден. Попробуйте позже.',
			]);
		}

		$route = $routes[$_SERVER['REQUEST_URI']];

		$controllerName = str_replace('/', '\\', Dispatcher::APP_CONTROLLERS_PATH . $route['controller']);
		$controller = new $controllerName();

		return $controller->{$route['method']}();

	}

	private function prepareApiGetData()
	{

		$urlComponents = parse_url($_SERVER['REQUEST_URI']);
		parse_str($urlComponents['query'], $_GET);

		$_SERVER['REQUEST_URI'] = str_replace([$urlComponents['query'], '?'], '', $_SERVER['REQUEST_URI']);

	}

}
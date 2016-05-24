<?php


	use Symfony\Component\Debug\ErrorHandler;
	use Symfony\Component\Debug\ExceptionHandler;
	use Silex\Provider\UrlGeneratorServiceProvider;


	// Register global error and exception handlers
	ErrorHandler::register();
	ExceptionHandler::register();


	// Register service providers.
	$app->register(new Silex\Provider\TwigServiceProvider(), array(
	    'twig.path' => __DIR__.'/../views',
	));
	$app->register(new UrlGeneratorServiceProvider());
	$app->register(new Silex\Provider\SessionServiceProvider());

	$_ENV['id'] = '50f1459e6eeeb809aa73';
	$_ENV['secret'] = 'f45642bc6cc6eccdb0f92bcaabbf1a3b7e21e260';
	$_SESSION['listeRepos'] = "";


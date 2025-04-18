.PHONY: init

init:
	composer install
	php bin/console lexik:jwt:generate-keypair
	php bin/console fos:elastica:populate
	php bin/console messenger:consume async

stop:
	docker stop phpmyadmin_gui nginx_server mysql_sharemoumouth php-8.0.9 symfony_cli
	docker rm phpmyadmin_gui nginx_server mysql_sharemoumouth php-8.0.9 symfony_cli

dev:
	docker-compose up -d --build server symfony mysql phpmyadmin

install:
	docker-compose run --rm composer install
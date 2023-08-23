build:
	docker build -f Dockerfile -t minecraft-query-client:latest .

test:
	docker run --rm -it -v $(shell pwd):/app minecraft-query-client /app/vendor/phpunit/phpunit/phpunit /app/tests
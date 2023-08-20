build:
	docker build -f Dockerfile -t minecraft-query-client:latest .

test:
	docker run --rm -it -v $(pwd):/app minecraft-query-client /app/vendor/phpunit /app/tests
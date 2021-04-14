.PHONY: start stop load-fixtures

.DEFAULT_GOAL := help

help:
	@fgrep -h "###" $(MAKEFILE_LIST) | fgrep -v fgrep | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

stop: ### Stop elastic cluster and kibana
	docker-compose stop

start: ### Start elastic cluster and kibana
	docker-compose up -d

load-fixtures: ### Load fixtures into ES
	if [ "$(docker images -q es-training-load-fixtures)" == "" ]; then docker build . --tag es-training-load-fixtures ; fi
	docker run --rm --network host -v ${PWD}:/app -w /app docker.io/library/es-training-load-fixtures sh -c "composer install && php load_fixtures.php"

.PHONY: all dev ci ci-install install build release watch start stop clean

all: install build

dev: install watch

ci: ci-install build

ci-install:
	composer install --no-dev --no-interaction --optimize-autoloader
	pnpm i

install:
	composer install
	pnpm i

build:
	pnpm run start
	pnpm run build-blocks

release: ci
	mkdir -p build
	zip build/jquest.zip -r * -x @zip_exclude.txt

watch:
	pnpm run watch

start:
	pnpm run wp:start

stop:
	pnpm run wp:stop

clean:
	rm -rf node_modules
	rm -rf build

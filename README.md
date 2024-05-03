# Skeleton #

A minimal project that allows you to quickly create a new project based on `Symfony` using useful tools: `standard-version`, `php-cs-fixer`, `phpstan`, `phpunit`, `docker`, `xdebug` and my own `starting script`. It is ready to support debugging with **Visual Studio Code** - just look into .env.local ;). 

### WHY?
Because personally, every time I start a new project, or a library for it, I have to do all this manually. For this, I made application skeletons for different uses: `cli`, `api`, `library`, and `bundle`, to speed up and standardize my own work. These skeletons uses this project.

### How to start

Create directory for your project, get in (`cd {Your directory}`) and run command:
```sh
composer create-project pbaszak/skeleton . --no-interaction
```

(Everytime) Start local environment using this command:
```sh
bash start.sh
```

and remove `CHANGELOG.md` (because it's owned by skeleton project. Your project will be have generated `CHANGELOG.md` after first release):
```sh
rm CHANGELOG.md
```
and voila! Your local environment is ready to development basic php app with useful tools.

### How to use **Standard Version**

If You don't have node_modules directory run:
```sh
npm install
```

First release:
```sh
npm run version:first-release
```

`Major`, `Minor`, `Patch` version update:
```sh
npm run version:major
# or
npm run version:minor
# or
npm run version:patch
```

Push tags:
```sh
npm run version:release
# or
npm run release
```

Check `package.json` for understand what commands do.

### How to use **PHPStan**

Main command:
```bash
docker exec php composer code:analyse
```
but, if You need to add errors to ignored:
```bash
docker exec php composer code:analyse:b
```

### How to use **PHP CS Fixer**

```bash
docker exec php composer code:fix
```

### How to use **XDebug** in **Visual Studio Code**

Create new file in Your project: `.vscode/launch.json`
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for xDebug",
            "type": "php",
            "request": "launch",
            "port": 5902,
            "hostname": "0.0.0.0",
            "pathMappings": {
                "/app/": "${workspaceRoot}"
            }
        }
    ]
}
```

Uncomment environments in `.env.local`:
```env
XDEBUG_MODE=develop,debug
XDEBUG_CONFIG=" client_port=5902 idekey=VSCODE client_host=host.docker.internal discover_client_host=0 start_with_request=yes"
```

Type `Ctrl + Shift + D` and run `Listen for xDebug`.

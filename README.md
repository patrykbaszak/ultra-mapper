# Ultra Mapper #

// description

## Usage

// in progress

## Attributes

### TargetProperty<hr>

The behavior of the `#[TargetProperty()]` attribute depends on the class in which you declare it (*origin*, *source*, *target*). The table below presents the relationship between the declaration place and the active process, and how the attribute changes the name (or path) of the property. Placing an attribute in an origin class has no effect on any process unless the origin class is also a source class, a target class, or both.

##### NAME

| Declaration place | Normalization | Denormalization | Mapping | Transformation |
|:-:|:-:|:-:|:-:|:-:|
| **Source** | Source: ✖️<br>Target: ✖️ | Source: ✔️<br>Target: ✖️ | Source: ✖️<br>Target: ✔️ | Source: ✖️<br>Target: ✔️ |
| **Target** | Source: ✖️<br>Target: ✔️ | Source: ✖️<br>Target: ✖️ | Source: ✔️<br>Target: ✖️ | Source: ✔️<br>Target: ✖️ |

##### PATH

| Declaration place | Normalization | Denormalization | Mapping | Transformation |
|:-:|:-:|:-:|:-:|:-:|
| **Source** | Source: ✖️<br>Target: ✖️ | Source: ✔️<br>Target: ✖️ | Source: ✖️<br>Target: ➖ | Source: ✖️<br>Target: ➖ |
| **Target** | Source: ✖️<br>Target: ➖ | Source: ✖️<br>Target: ✖️ | Source: ➖<br>Target: ✖️ | Source: ✔️<br>Target: ✖️ |

###### legend: ✖️ - *has no effect*, ✔️ - *affects*, ➖ - *not implemented*

## How it works

// in progress

## Performance Report

// in progress

## Development

### How to start

Start local environment using this command:
```sh
bash start.sh
```

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

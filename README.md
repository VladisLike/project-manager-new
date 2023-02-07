## Installation

You need to use make command for deploy project using docker-compose:

```bash
make init
```

Go to bash:

```bash
make exec_bash
```

Into bash run following commands:

```bash
composer install
```

```bash
rm -r var/cache
```

### For development mode:

#### Run into /app#
```bash
make app-run
```

Run server in browser: http://localhost:8088
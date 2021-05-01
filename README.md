Shop: API Service
--
### Description:
The API Gateway for marketplace system.

---

### Installation:

1. Clone the repository:
```shell script
git clone git@github.com:marketplace-system/shop_api.git
```

2. Rename the file “.env.example” under “/” to “.env” then change the parameters to match your preferences, example:
```yaml
#RABBITMQ CONNECTION PARAMS
RABBITMQ_HOST=
RABBITMQ_PORT=
RABBITMQ_USER=
RABBITMQ_PASS=

#REDIS CONNECTION PARAMS
REDIS_HOST=
REDIS_PORT=
REDIS_PASSWORD=
...
```
>Note: You can use network (marketplace-network) gateway ip instead of providing each container ip


3. Build a new image:
```bash
docker-compose build
```

4. Run the containers
```bash
docker-compose up -d
```


If you want to scale up the workers (sync / async), you can simply run this command:
```bash
docker-compose up --scale api-interface=num -d
```

Where “num” is the number of processes to run, example:
```bash
docker-compose up --scale api-interface=3 -d
```

---
### Unit test
To run the unit test, just run this command:
```bash
docker-compose up api-unit-test
```

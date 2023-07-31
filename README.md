# ICA
Work in progress. Internet Content Aggregator v3.
This is the draft of the new version of https://i-c-a.su

## Deploy: 
- development: 
  - build: `docker build -t xtrime/ica:latest -f Dockerfile . && docker build -t xtrime/ica:dev -f Dockerfile-dev .`
  - deploy: `docker compose -f docker-compose.yml -f docker-compose.dev.yml up`
- production: `docker-compose up -d --build`

## Update
- `docker-compose exec -e 'XDEBUG_MODE=off' ica composer update`
- `docker-compose exec npm npm update`

## TODO
- User sources: check and add
- Frontend request batching
- Sources stats increment
- Email styles
- Parsing queue and intervals
- Migrate data from legacy site
- Pagination
- Filters for feed

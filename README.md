# ICA
Work in progress. Internet Content Aggregator v3.
This is the draft of the new version of https://i-c-a.su

## Deploy: 
- development: `docker-compose -f docker-compose.yml -f docker-compose.dev.yml up`
- production: `docker-compose up -d`

## Update
- `docker-compose exec -e 'XDEBUG_MODE=off' ica composer update`
- `docker-compose exec npm npm update`

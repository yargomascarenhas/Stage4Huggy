# Stage4Huggy
Consume Zendesk API for store reports
This repository provide a resolution for Huggy Challenge Stage 4

## Proposal
- Development a Zendesk API Integration to extract reports
- Create a Dashboard to show all tickets with data, assignees and status filters
- Metabase is agreed for generate Dashboard

## Setup

### Database
- Create a database called "stage4huggy"
- Run script_database.sql file

### API
- Clone this repository
- In terminal do "composer install" to install dependencies
- Run local server
- The API has 2 endpoints
    - /v1/zendesk/users (populate user table)
    - /v1/zendesk/tickets (populate ticket table)


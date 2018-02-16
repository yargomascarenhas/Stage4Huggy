# Stage4Huggy
Consume Zendesk API for store reports
This repository provide a resolution for Huggy Challenge Stage 4

[Link da aplicação](http://stage4huggy.s3-website-us-east-1.amazonaws.com/)

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
- Endpoints
    - Zendesk
        - GET /v1/zendesk/users (populate user table)
        - GET /v1/zendesk/tickets (populate ticket table)
        - GET /v1/zendesk/organizations
    - Users
        - GET /v1/users
        - GET /v1/users/{id}
        - POST /v1/users/login
    - Organizations
        - GET /v1/organizations
        - GET /v1/organizations/{id}
    - Tickets
        - GET /v1/tickets
        - GET /v1/Tickets/{id}
        - GET /v1/groupstatus
        - GET /v1/groupstatisfaction

### Front
- inside de directory /fronthuggy
- In terminal do "ng serve" for run locally or "ng build" to deploy

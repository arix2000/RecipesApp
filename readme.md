# Recipes App
This is a Symfony application that uses Docker for containerization and npm for managing front-end dependencies.

## Prerequisites

Before you can start the application, you need to have the following software installed on your machine:

1. [Docker](https://www.docker.com/get-started) - Platform for developing, shipping, and running applications in containers.
2. [Docker Compose](https://docs.docker.com/compose/install/) - Tool for defining and running multi-container Docker applications.
3. [Node.js and npm](https://nodejs.org/) - JavaScript runtime built on Chrome's V8 JavaScript engine and the package manager for Node.js, respectively.

## Getting Started

Follow these instructions to get the application up and running:

### 1. Install Docker

Follow the instructions on the [Docker installation page](https://www.docker.com/get-started) to install Docker on your machine.

### 2. Install Docker Compose

Follow the instructions on the [Docker Compose installation page](https://docs.docker.com/compose/install/) to install Docker Compose.

### 3. Install Node.js and npm

Follow the instructions on the [Node.js installation page](https://nodejs.org/) to install Node.js, which includes npm.

### 4. Run the shell script

Navigate to the project directory and run the provided shell script to start the application. This script will use Docker Compose to build and run the necessary containers:

```bash
./start-app.sh
```

Once the containers are up and running, you can access the application in your web browser at:

```
http://localhost:8000
```
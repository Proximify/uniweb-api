# UNIWeb API Client

<img src="docs/assets/uniweb_logo.svg" width="200px" alt="UNIWeb API logo" align="center">

## Overview

The UNIWeb API allows institutions to seamlessly integrate UNIWeb with their existing systems. This repository provides both the API documentation and a reference PHP client implementation.

### Key Features

- **Secure Access Control**: Manage API access permissions at the institutional level
- **Read/Write Operations**: Full support for reading and updating institutional data
- **JSON Format**: Clean, well-structured JSON responses for easy integration
- **Flexible Filtering**: Query specific data subsets to optimize response times
- **Reference Implementation**: PHP client library with example use cases

## Quick Start

### Installation Options

#### Option 1: Create a New Project (Recommended for Testing)

```bash
# Creates a new project with example code and CLI testing tools
composer create-project proximify/uniweb-api

# Optional: specify custom installation path
composer create-project proximify/uniweb-api /custom/path
```

#### Option 2: Add as a Dependency

```bash
# Add to an existing project
composer require proximify/uniweb-api
```

## Setup Guide

### 1. Get Your API Credentials

1. Log into your UNIWeb instance
2. Navigate to Administration → API section
3. Click "Create a client"
4. Enter a client name and save
5. Click "View" on your new client to reveal the secret key

### 2. Configure Credentials

Create `settings/credentials.json`:

```json
{
    "clientName": "your_client_name",
    "clientSecret": "your_client_secret",
    "homepage": "your_uniweb_instance_url"
}
```

⚠️ **Security Note**: Add `settings/credentials.json` to your `.gitignore` file

### 3. Run Example Queries

#### Using the Web Interface

1. Navigate to the project directory
2. Start PHP's built-in server:
   ```bash
   cd www
   php -S localhost:8000
   ```
3. Open `http://localhost:8000` in your browser
4. Select and run example queries through the web interface

#### Using the CLI

```bash
# Run a specific example query
composer query example3
```

## PHP Client Usage

```php
$credentials = [
    "clientName" => "your_client_name",
    "clientSecret" => "your_client_secret",
    "homepage" => "your_uniweb_instance_url"
];

$client = new UniwebClient($credentials);
```

## Documentation

For detailed API documentation, see [Complete UNIWeb API Documentation](docs/uniweb-api.md).

## Sample Use Cases

The `queries` folder contains example code for common API operations:
- Fetching user profiles
- Updating research activities
- Retrieving publication data
- And more...

## API Capabilities

The UNIWeb API provides:
- Secure authentication
- User data management
- Research activity tracking
- Publication management
- Institutional data access
- Custom data filtering

## Support

For technical questions or issues:
1. Check the [API Documentation](docs/uniweb-api.md)
2. Create an issue in this repository
3. Contact UNIWeb support through your institutional channels

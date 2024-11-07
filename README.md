<p align="center">
  <img src="docs/assets/uniweb_logo.svg" width="200px" alt="uniweb API logo">
</p>

# UNIWeb API Client

## Overview

The UNIWeb API allows institutions to seamlessly integrate UNIWeb with their existing systems. This repository provides both the API documentation and a reference PHP client implementation.

### Key Features

- **Secure Access Control**: Manage API access permissions at the institutional level
- **Read/Write Operations**: Full support for reading and updating institutional data
- **JSON Format**: Clean, well-structured JSON responses for easy integration
- **Flexible Filtering**: Query specific data subsets to optimize response times
- **Reference Implementation**: PHP client library with example use cases

## Implementation Options

### 1. Simple Integration

Before implementing the API, consider if you just need to embed UNIWeb content. You can use the embed parameter:

```html
<iframe
  src="https://your-uniweb-instance.com/embed/profile/members/[MEMBER_ID]"
  frameborder="0"
  width="80%"
  height="600px"
></iframe>
```

### 2. Using Postman or Direct HTTP Requests

The simplest way to test and use the API is through direct HTTP requests. Here are some common examples:

```bash
# Get user profile information
GET {{BASE_API_URL}}/resource.php?action=read
    &resources[]=profile/affiliations
    &resources[]=profile/membership_information
    &id=833

# Get members by unit and title
GET {{BASE_API_URL}}/resource.php?action=read
    &resources[]=profile/affiliations
    &resources[]=profile/membership_information
    &filter[unit]=University of XYZ
    &filter[title]=Professor

# Get all members
GET {{BASE_API_URL}}/resource.php?action=getMembers&onlyPublic=0

# Get profile picture
GET {{BASE_API_URL}}/picture.php?action=display
    &contentType=members
    &id=833
    &quality=large
```

### 3. Using the PHP Client

Begin by installing [PHP Composer](https://getcomposer.org/download/) if you don't already have it.

#### Option 1: Create a New Project (Recommended for Testing)

```bash
# Creates a new project with example code and CLI testing tools
composer create-project proximify/uniweb-api

# Optional: specify custom installation name or path
composer create-project proximify/uniweb-api my-test
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

> [!TIP]
> Review the Profile and CV data schemas located at Administration → Schemas

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

## Resource Paths

Resources are identified by paths following this structure:

```
page/section/subsection/...
```

Example paths:

- `profile/affiliations`
- `cv/education/degrees`
- `profile/membership_information`

### Content Types

- `members`: Individual user data
- `units`: Organizational unit data
- `groups`: Group-related data

## Common Operations

### Reading Data

Send as GET or POST parameters.

```json
{
  "action": "read",
  "content": "members",
  "filter": {
    "unit": "Civil Engineering"
  },
  "resources": ["profile/biography"]
}
```

### Filtering Options

- `unit`: Filter by department/unit
- `title`: Filter by title (e.g., "Professor")
- `loginName`: Filter by username/email
- `modified_since`: Filter by modification date
- `onlyPublic`: Include/exclude private data

### Language Support

Add `language` parameter:

- `en`: English responses
- `fr`: French responses

## Error Handling

Errors are returned in JSON format:

```json
{
  "error": {
    "message": "Error description",
    "type": "ErrorType",
    "code": 98,
    "error_subcode": 223
  }
}
```

Common error codes:

- 98: OAuth token validation error
- 401: Unauthorized
- 403: Insufficient permissions
- 404: Resource not found

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

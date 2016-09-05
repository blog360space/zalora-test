# Zalora test

## Feature
- GET/api/file
   - List available file on server:
- POST /api/file/upload
   - Upload file. 
  - If file uploaded is duplicated, not to save the new file to save space.
- GET /api/file/download/{original_name}
  - Download file by name
- DELETE /api/file/delete/{originalName}
  - Delete file by name on server
- Swagger document interaction
- Unitest interaction

## How to install:
- Download from github
```sh
$ git clone git@github.com:hunggau/zalora-test.git
```
- Edit .env file
- Grand permission writable to directories storage and cache:

```sh
$ sudo chown www-data:www-data -R storage/
$ sudo chown www-data:www-data -R bootstrap/cache
$ sudo chmod 755 -R storage/
$ sudo chmod 755 -R bootstrap/cache/
```

- Run command to install database
```sh
$ php artisan migrate 
```

## Author
Hung Tran, email: mhungou04[at]gmail.com
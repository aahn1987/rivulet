#!/bin/bash
# =============================================================
# Rivulet API Framework (v1.0.0) - Structure Initialization
# =============================================================
# This script creates the complete directory and file structure
# for the Rivulet framework. Run it inside the main project
# directory (e.g., inside 'rivulet/').

echo "Initializing Rivulet framework structure... 🌊"

# --- Create Main Directories ---
echo "Creating main directories..."
mkdir -p app/{Controllers,Events,Helpers,Jobs,Listeners,Middleware,Models,Rules,Services}
mkdir -p bootstrap
mkdir -p config
mkdir -p core/{Console/Commands,Database/{Relations,Operations,Migrations},Events,Filesystem/Operations,Http/{Cookies,Session,Client},Mail/Drivers,Middleware/{Auth,RateLimiting},Notifications/Drivers,Providers,Queue/Drivers,Routing,System/{Logging,Cache,Debug},Support,Validation/Rules,Views}
mkdir -p database/{Migrations,Seeders}
mkdir -p public
mkdir -p resources/{css,js,images,views}
mkdir -p routes
mkdir -p storage/{logs,cache,uploads}
mkdir -p tests

# --- Create Application Files (app/) ---
echo "Creating application files..."
touch app/Controllers/UsersController.php
touch app/Events/UsersEvent.php
touch app/Helpers/UsersHelpers.php
touch app/Jobs/UsersJobs.php
touch app/Listeners/USerListener.php
touch app/Middleware/UsersMIddleware.php
touch app/Models/Users.php
touch app/Rules/UsersRules.php
touch app/Services/UsersServices.php

# --- Create Bootstrap File ---
touch bootstrap/bootstrap.php

# --- Create Config Files (config/) ---
echo "Creating configuration files..."
touch config/{app,middleware,cookies,database,events,filesystems,logging,mail,queue,routes,schedule,services,session,views}.php

# --- Create Core System Files (core/) ---
echo "Creating core system files..."
# Console
touch core/Console/Console.php
touch core/Console/Commands/{CacheClear,ConfigCache,ConfigClear,Create,CreateController,CreateEvent,CreateJob,CreateListener,CreateModel,CreateMiddleware,CreateResource,CreateRule,CreateSeeder,CreateService,CreateTemplate,DatabaseMigrate,DatabaseRollback,DatabaseSeed,KeyGenerate,LogsClear,Optimize,Poke,QueueWork,RoutesCache,RoutesClear,RoutesList,RunServer,ScheduleRun,StorageLink,TestRun}.php

# Database
touch core/Database/Connection.php
touch core/Database/QueryBuilder.php
touch core/Database/Relations/{BelongsTo,BelongsToMany,HasMany,HasOne}.php
touch core/Database/Operations/{SelectOperation,InsertOperation,UpdateOperation,DeleteOperation,CreateTable,AlterTable,DropTable,AddColumn,UpdateColumn,DeleteColumn}.php
touch core/Database/Migrations/{SchemaBuilder,ColumnBuilder,SeedOperation,Migration,Runner}.php

# Events
touch core/Events/{Dispatcher,Event,Listener}.php

# Filesystem
touch core/Filesystem/Filesystem.php
touch core/Filesystem/Operations/{CreateFile,Upload,Rename,Copy,Move,DeleteFile,Download,Zip,Extract,CreateDirectory,DeleteDirectory}.php

# HTTP
touch core/Http/Kernel.php
touch core/Http/Request.php
touch core/Http/Response.php
touch core/Http/Cookies/Cookies.php
touch core/Http/Session/Session.php
touch core/Http/Client/Client.php

# Mail
touch core/Mail/Mailer.php
touch core/Mail/Drivers/{MailgunDriver,PhpMailDriver,SendGridDriver,SendmailDriver,SmtpDriver}.php

# Middleware
touch core/Middleware/Middleware.php
touch core/Middleware/AuthMiddleware.php
touch core/Middleware/RateLimitMiddleware.php
touch core/Middleware/Auth/Auth.php
touch core/Middleware/RateLimiting/RateLimit.php

# Notifications
touch core/Notifications/Notification.php
touch core/Notifications/Drivers/{FirebaseDriver,PusherDriver,SlackDriver,SmsDriver,WhatsappDriver}.php

# Providers
touch core/Providers/{AppServiceProvider,CookiesServiceProvider,DatabaseServiceProvider,EventServiceProvider,FilesystemServiceProvider,HttpClientServiceProvider,MailServiceProvider,NotificationServiceProvider,QueueServiceProvider,RouteServiceProvider,SessionServiceProvider,ViewsServiceProvider,ServiceProvider}.php

# Queue
touch core/Queue/Queue.php
touch core/Queue/Scheduler.php
touch core/Queue/Job.php
touch core/Queue/Drivers/{DatabaseQueue,RedisQueue}.php

# Routing
touch core/Routing/Router.php
touch core/Routing/Route.php

# System
touch core/System/Logging/Logs.php
touch core/System/Cache/Cache.php
touch core/System/Debug/Debug.php

# Support
touch core/Support/Helpers.php

# Validation
touch core/Validation/Validator.php
touch core/Validation/Rules/{Alpha,Alphanum,Arr,Between,Bool,Date,Email,File,FileSize,Integer,Ip,Max,Min,Regex,Required,String,Url}.php

# Views
touch core/Views/Engine.php
touch core/Views/View.php

# Base Files
touch core/Controller.php
touch core/Model.php
touch core/Config.php
touch core/Rivulet.php

# --- Create Database Files (database/) ---
echo "Creating database files..."
touch database/Migrations/2025_09_01_create_users_table.php
touch database/Migrations/2025_09_01_create_jobs_table.php
touch database/Seeders/UsersSeeder.php

# --- Create Public Files (public/) ---
echo "Creating public files..."
touch public/.htaccess
touch public/index.php

# --- Create Resource Files (resources/) ---
echo "Creating resource files..."
touch resources/css/style.css
touch resources/js/main.js
touch resources/images/logo.png
touch resources/views/{404,welcome,unauthorized}.html

# --- Create Routes File (routes/) ---
touch routes/api.php

# --- Create Root-Level Files ---
echo "Creating root-level files..."
touch .env
touch .env.example
touch composer.json
touch Dockerfile
touch luna

echo "✅ Rivulet framework structure created successfully!"
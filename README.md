

---

#  Project Setup with Docker and Sail

This project is developed in Laravel 11  in docker.

## Prerequisites

Before starting, ensure you have the following installed on your machine:

- **Docker**
- **Docker Compose**
- **Git**
- **PHP 8.2/8.3**



### Project setup

First, clone your Laravel project or create a new one.

To clone an existing project:

```bash
git clone https://github.com/monsur22/apex-api.git
cd apex-api
```
Rename .env.dev to .env.\
Up all the  container of Docker:

```bash
sail up -d
```
check all container list:

```bash
docker ps -a
```

Go to the php container:
```bash
docker exec -it container_id bash
```
Then install composer:

```bash
composer install
```
Run Migration fresh seed:

```bash
php artisan migrate:fresh --seed
```
All api end point: 

```bash
*-------auth route------*
api/register
api/login 
api/logout
api/refresh
api/me 

*-------order route------*
api/orders 
api/orders/history

*-------product route------*
api/products 
api/products 
api/products/{product}
api/products/{product} 
api/products/{product} 
```

#  Task details
1. Use service pattern. Create service interface and service class for business logic.
2. Return json response from resources file by using laravel resource.
3. Using Laravel ORM for relation.
4. Using Request file for validation.
5. Create cutom middlewire for check user permission.
6. Using CACHE_DRIVER redis 
7. There are two role admin and customer
8. After migration seed some demo admin, user and product will be create.
9. Admin can CRUD all product.
10. User can only crate order and seed his/her order history.
11. Database use Mysql.

### 1. User Authentication (JWT):
* Register
```
body: 
{
    "name":"User",
    "email": "user4@email.com",
    "password":"12345678",
    "password_confirmation": "12345678"     
}
reponse:
{
    "user": {
        "id": 5,
        "name": "User",
        "email": "user4@email.com",
        "created_at": "2024-11-12T14:17:12.000000Z",
        "updated_at": "2024-11-12T14:17:12.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9yZWdpc3RlciIsImlhdCI6MTczMTQyMTAzMiwiZXhwIjoxNzMxNDI0NjMyLCJuYmYiOjE3MzE0MjEwMzIsImp0aSI6IkgzMGl1WlM1UkNhdGdrQTciLCJzdWIiOiI1IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.Nfbsb-Zy1gGZ4s1arwoEuW8CF_hECmb8tdSxqPHn1DQ"
}
```
* Login
```
body: 
{
    "email": "customer@example.com",
    "password":"12345678"
}
reponse:
{
    "user": {
        "id": 2,
        "name": "Customer User",
        "email": "customer@example.com",
        "created_at": "2024-11-12T13:02:35.000000Z",
        "updated_at": "2024-11-12T13:02:35.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9sb2dpbiIsImlhdCI6MTczMTQyMTIyMiwiZXhwIjoxNzMxNDI0ODIyLCJuYmYiOjE3MzE0MjEyMjIsImp0aSI6InJ1bW9ETWt6ekNXWlVYMWEiLCJzdWIiOiIyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.vgID7YKCe36NR9YxegrEJCJoL5xA5yZjs3H2Kb7vfUk"
}
```
* Token Refresh
```
reponse:
{
   
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9yZWZyZXNoIiwiaWF0IjoxNzMxNDIxMjIyLCJleHAiOjE3MzE0MjQ5MjcsIm5iZiI6MTczMTQyMTMyNywianRpIjoicEp5WUljTFdUZHA0NGFPNiIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.z2-HU2gaHiG0XR8lXF91mHdYty7Z7tiOnVrkDIYnj5k",
    "token_type": "bearer",
    "expires_in": 3600

}
```
### 2. Product Management:
* List all products (publicly available).
```
{
    "success": true,
    "message": "Request was successful",
    "data": [
        {
            "id": 1,
            "name": "Product 1",
            "price": "200.00",
            "stock": 96,
            "created_at": "2024-11-12T13:02:35.000000Z",
            "updated_at": "2024-11-12T13:07:25.000000Z"
        },
        {
            "id": 2,
            "name": "Product 2",
            "price": "49.99",
            "stock": 48,
            "created_at": "2024-11-12T13:02:35.000000Z",
            "updated_at": "2024-11-12T13:14:39.000000Z"
        },
        {
            "id": 3,
            "name": "Product 3",
            "price": "19.99",
            "stock": 197,
            "created_at": "2024-11-12T13:02:35.000000Z",
            "updated_at": "2024-11-12T13:14:39.000000Z"
        }
    ]
}
```
* Admin-only: Create/Update a product.\
Create Product:\
Customer:
```
{
    "message": "You don't have permission to access this resource."
}
```
Admin:
```

   {
    "success": true,
    "message": "Product created successfully",
    "data": {
        "id": 5,
        "name": "Product 2",
        "price": 100,
        "stock": 20,
        "created_at": "2024-11-12T14:25:22.000000Z",
        "updated_at": "2024-11-12T14:25:22.000000Z"
    }
}

```
Update Product:\
Customer:
```
{
    "message": "You don't have permission to access this resource."
}
```
Admin:
```

{
    "success": true,
    "message": "Product updated successfully",
    "data": {
        "id": 1,
        "name": "Product 1",
        "price": 200,
        "stock": 96,
        "created_at": "2024-11-12T13:02:35.000000Z",
        "updated_at": "2024-11-12T14:26:16.000000Z"
    }
}


```


### 3. Order Management:
* Place an order (authenticated users only).
Unauthenticated User:
```
{
    "message": "Unauthenticated."
}
```
Authenticated User:
```

{
    "success": true,
    "message": "Request was successful",
    "data": {
        "id": 4,
        "user_id": 2,
        "total_amount": 419.99,
        "created_at": "2024-11-12T14:28:53.000000Z",
        "updated_at": "2024-11-12T14:28:53.000000Z",
        "items": [
            {
                "id": 7,
                "order_id": 4,
                "product_id": 1,
                "quantity": 2,
                "price": "200.00",
                "total": 400,
                "product": {
                    "id": 1,
                    "name": "Product 1",
                    "price": "200.00",
                    "stock": 94,
                    "created_at": "2024-11-12T13:02:35.000000Z",
                    "updated_at": "2024-11-12T14:28:53.000000Z"
                }
            },
            {
                "id": 8,
                "order_id": 4,
                "product_id": 3,
                "quantity": 1,
                "price": "19.99",
                "total": 19.99,
                "product": {
                    "id": 3,
                    "name": "Product 3",
                    "price": "19.99",
                    "stock": 196,
                    "created_at": "2024-11-12T13:02:35.000000Z",
                    "updated_at": "2024-11-12T14:28:53.000000Z"
                }
            }
        ]
    }
}


```

* View order history for the logged-in user
Unauthenticated User:
```
{
    "message": "Unauthenticated."
}
```
Authenticated User:
```
{
    "success": true,
    "message": "Order history fetched successfully",
    "data": [
        {
            "id": 1,
            "user_id": 2,
            "total_amount": "419.99",
            "created_at": "2024-11-12T13:05:50.000000Z",
            "updated_at": "2024-11-12T13:05:50.000000Z",
            "items": [
                {
                    "id": 1,
                    "order_id": 1,
                    "product_id": 1,
                    "quantity": 2,
                    "price": "200.00",
                    "total": 400,
                    "product": {
                        "id": 1,
                        "name": "Product 1",
                        "price": "200.00",
                        "stock": 94,
                        "created_at": "2024-11-12T13:02:35.000000Z",
                        "updated_at": "2024-11-12T14:28:53.000000Z"
                    }
                },
                {
                    "id": 2,
                    "order_id": 1,
                    "product_id": 3,
                    "quantity": 1,
                    "price": "19.99",
                    "total": 19.99,
                    "product": {
                        "id": 3,
                        "name": "Product 3",
                        "price": "19.99",
                        "stock": 196,
                        "created_at": "2024-11-12T13:02:35.000000Z",
                        "updated_at": "2024-11-12T14:28:53.000000Z"
                    }
                }
            ]
        },
        {
            "id": 4,
            "user_id": 2,
            "total_amount": "419.99",
            "created_at": "2024-11-12T14:28:53.000000Z",
            "updated_at": "2024-11-12T14:28:53.000000Z",
            "items": [
                {
                    "id": 7,
                    "order_id": 4,
                    "product_id": 1,
                    "quantity": 2,
                    "price": "200.00",
                    "total": 400,
                    "product": {
                        "id": 1,
                        "name": "Product 1",
                        "price": "200.00",
                        "stock": 94,
                        "created_at": "2024-11-12T13:02:35.000000Z",
                        "updated_at": "2024-11-12T14:28:53.000000Z"
                    }
                },
                {
                    "id": 8,
                    "order_id": 4,
                    "product_id": 3,
                    "quantity": 1,
                    "price": "19.99",
                    "total": 19.99,
                    "product": {
                        "id": 3,
                        "name": "Product 3",
                        "price": "19.99",
                        "stock": 196,
                        "created_at": "2024-11-12T13:02:35.000000Z",
                        "updated_at": "2024-11-12T14:28:53.000000Z"
                    }
                }
            ]
        }
    ]
}

```
Postman collection: Apex-Api.postman_collection.json
Hello, 
I Completed The Task Successfully. To Cheak ,
1.Clone the Project First and Install Composer
2.Run Migration 
3.php artisan ser
http://127.0.0.1:8000   //replace with your url

Post->http://127.0.0.1:8000/api/users    //Create User
      name,account_type,password,email

Post->http://127.0.0.1:8000/api/login    //login
     password,email


// using token
Post->http://127.0.0.1:8000/api/deposit   //deposit
    user_id,amount

Post->http://127.0.0.1:8000/api/withdrawal  //withdrawal
    user_id,amount

Get->http://127.0.0.1:8000/api/transactions  //transactions


Get->http://127.0.0.1:8000/api/withdrawal/transactions  //withdrawal/transactions 

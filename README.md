

## About Project

1. First we are making a Laravel Project  -  use official laravel docs for installation and set up a laravel project
2. We are instaling the requirements from the official Stripe Docs - Stripe Accept Payments
3. You would need your own account in stripe so you can use the key
4. Go to .env and change the key to your key from the account on stripe, also the webhook
5. We are dowloading Stripe CLI
6. When you dowload the CLI you are opening the folder and you will see stripe.exe you are opening your terminal (cmd) and going cd -> path of the folder which is stripe.exe
7. Runing the cmd stripe.exe login
8. If you done correct you will see a link which you will open on your browser and the following sentence will be same as in the cmd so you know you are connected (Auth)
9.  After that in the docs of stripe webhook you will follow the second step which is ` Forward evennts to your webhook `
10.  You are copy paste that in new cmd cd -> the path of stripe.exe and you are pasting but the path should be a port of :8000/ path of the hook ex. :8000/webhook
11.  When you hit enter you will get some key you need to copy it and then replace it in the .env folder
12.  After that you are going to the final step 3 copy paste to the cmd and test it if is succeeded you are on the right path.

For any issues just contact me thanks for looking to my project.
   




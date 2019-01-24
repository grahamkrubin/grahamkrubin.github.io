# Students Involved
- Graham Rubin - grahamkrubin@wustl.edu
- Robert Landlord - robertlandlord@wustl.edu


# Maximizing Portfolio Returns with Python/Django

This application is used to find your optimal portfolio based on a maximized sharpe ratio and minimized volatility given a bundle of stocks that you choose

# Basics:

Users can first find very basic functionality without opening up an account. They may:

1. Look at the beautiful visuals of the site :-)

2. Find any publicly traded stock (given that it is within the yahoo finance api), and get some recent information for it

3. Contact the creators of the website 

However, upon creating an account, users will be met with lots of additional features including:
1. Portfolio Creation through adding stocks to your own portfolio
2. Optimization of holdings - we will calculate optimal holdings for each bundle of stocks you give us
3. Return information - we will give you recent information about all stocks in your portfolio
4. Liquidate portfolio - you have the ability to liquidate all assets you own
5. All the non-user features :-)

# Dear USER,
There are a just few things you need to do to get started.

1. Clone the repository

2. Navigate to the mysite folder using terminal or cmd, and run "python manage.py runserver" - MAKE SURE YOU HAVE PYTHON 3.6 + installed!
2a. If you use Anaconda or miniconda, you may need to replace "python" with: "/path/to/miniconda/python.app"

3. Install any dependencies that your terminal says you are missing (the best way is through "pip install")

4. Navigate to the server (hosted locally) at the in-terminal site; should be at localhost around 127.0.0.1: 8000, or something similar

5. Have fun with the site!

# Directories
* mysite
    * this directory contains MAIN baseline requirements, like some urls and settings and the like
* personal
    * LOTS of functionality in this directory pertaining to user accounts + some data analysis
* portfolio
    * directory used for single stock searchup, ie adding and finding stocks
* blog
    * originally was going to be a "blog/chatroom" where users could discuss strategy
    * main skeleton and code is there (can be visited at xxx.x.x.x: xxx/blog)
    * HOWEVER, we did not finish the form/user showing, so this was excluded from the main site

# Creative Portion

1. Additional Frameworks/APIS/Concepts
    * In creating this site, we realized that there were many, many other frameworks and APIs out there, and that combining some of them together would make the best result.
    * REST Framework with Django
        * Usage of djangorestframework through GET and POST requests
        * Combined with usage of Jquery AJAX on client side
        * Allowed for quick retrieval of server side (views.py) data
    * Chart.js
        * Made beautiful charts with this javascript API rather than python's matplotlib
        * Charts are interactive, readable, and aesthetically pleasing
    * Bootstrap CSS
        * Made the site even more beautiful with some bootstrap structuring for data and site structure
    * Regex Navigation
        * Unlike hard coded URLS of the past, Django's framework allows for regex integration
        * Site navigation becomes more intuitive and wider coverage (if needed)
    * SQL Server integration
        * All data is saved within Django's SQL server
        
2. Additional Features:

    * Find Stock Feature
        * All users of the site (including those without accounts) can search up and see information for ANY stock
        * This gives much more flexibility to users with AND without accounts
        * They no longer have to go to a different website to search up such information 
    * Additional Rankings
        * Portfolio main page shows additional data not required by rubric
        
3. More charts and data analysis:

    * Instead of optimizing merely by returns, this portfolio optimizes based on a Markowitz portfolio
        * It runs simulations (essentially a *very* basic Monte-Carlo simulation) to optimize your portfolio in a variety of conditions
    * Three charts available for your viewing pleasure:
        * Aside from the main sharpe-optimization chart, we have returns graph and a min-volatility chart too!
    






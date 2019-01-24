from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.views.generic import View
from .forms import *
from portfolio.models import Portfolio
from rest_framework.views import APIView
from rest_framework.response import Response
import numpy as np
import matplotlib.pyplot as plt
import scipy.optimize as sco
import datetime as dt
from yahoo_fin.stock_info import *

# HERE IS THE MEAT :-)

# Create your views here.

#homepage
def index(request):
    return render(request, 'personal/home.html')    
#contact
def contact(request):
    return render(request, 'personal/basic.html', {'content':['If you would like to contact me, please contact me at ', 'robertlandlord@gmail.com', 'or', 'grahamrubin@wustl.edu']})
#logout
def logout_user(request):
    logout(request)
    return redirect('index')
#registration of users
class UserFormView(View):
    form_class = UserForm
    template_name = 'personal/registration_form.html'

    #display blank form
    def get(self, request):
        form = self.form_class(None)
        return render(request, self.template_name, {'form': form})
    #process the form data
    def post(self, request):
        form = self.form_class(request.POST)

        if form.is_valid():

            user = form.save(commit=False) #storing user info locally

            # cleaning data
            username = form.cleaned_data['username']
            password = form.cleaned_data['password']
            #change password
            user.set_password(password)
            user.save()


            #returns User objects if credentials are correct

            user = authenticate(username=username, password=password)

            if user is not None:
                if user.is_active:
                    login(request, user)
                    # also need to make them a portfolio
                    portfolio = Portfolio(user=User.objects.get(username=username))
                    portfolio.save()
                    return redirect('index') # redirecting you back to the home page

        return render(request, self.template_name, {'form': form})
#login page
class LoginFormView(View):
    form_class = LoginForm
    template_name = 'personal/login_form.html'

    #display blank form
    def get(self, request):
        form = self.form_class(None)
        return render(request, self.template_name, {'form': form})
    #process the form data
    def post(self, request):
        form = self.form_class(request.POST)

        if form.is_valid():

            #user = form.save(commit=False) #storing user info locally

            username = form.cleaned_data['username']
            password = form.cleaned_data['password']


            #returns User objects if credentials are correct
            # again, authenticating
            user = authenticate(username=username, password=password)

            if user is not None:
                if user.is_active:
                    login(request, user)
                    return redirect('index') # redirecting you back to the home page
            else:
                return render(request, self.template_name ,
                                          {'error_message': "<ul class='errorlist'> <li> Error occurred, please try again! </li> </ul>", 'form': form})

        return render(request, self.template_name, {'form': form})

class PassChangeFormView(View):
    form_class = PassChangeForm
    template_name = 'personal/password_change_form.html'

    #display blank form
    def get(self, request):
        form = self.form_class(None)
        return render(request, self.template_name, {'form': form})
    #process the form data
    def post(self, request):
        form = self.form_class(request.POST)

        if form.is_valid():


            # cleaned (normalized) data
            # get the current user's user object from database
            user = request.user
            username = user.username
            password = form.cleaned_data['password']
            password_check = form.cleaned_data['password_check']

            #changing password
            if password == password_check: #if the two typed passwords were the same
                user.set_password(password)
                user.save()
            else:
                return render(request, self.template_name,
                              {'error_message': "<ul class='errorlist'> <li> Passwords did not match! </li> </ul>",
                                  'form': form})
            #returns User objects if credentials are correct

            user = authenticate(username=username, password=password)

            if user is not None:
                if user.is_active:
                    login(request, user)
                    return redirect('index') # redirecting you back to the home page

        return render(request, self.template_name, {'form': form})




#this gives the big left side chart data
class ReturnChartData(APIView):

    def get(self, request, format=None):
        if request.user.is_authenticated:
            user_portfolio = Portfolio.objects.get(user=request.user)  # get portfolio associated with account
            stocklist = user_portfolio.stocks
            stocklist.sort()
            labels = stocklist

            today = dt.date.today().strftime('%m-%d-%Y')
            table = pd.DataFrame() # get our date indices
            data = get_data(stocklist[0], start_date='01/01/2016', end_date=today, index_as_date=False)
            dates = data["date"][::-1]

            for stock in stocklist: #get all the data starting from 2016
                data = get_data(stock, start_date='01/01/2016', end_date=today, index_as_date=False)['adjclose'][::-1]
                table[stock.upper()] = pd.Series(data)
            table.set_index(dates, 'date', inplace=True)
            data = {
                "labels": labels,
                "datasets": table,
                "dates": dates,
            }
            return Response(data)
        else:
            data = {
                "labels" : [],
                "datasets": [],
                "dates": [],
            }
            return Response(data)

# This was code based off of: https://towardsdatascience.com/efficient-frontier-portfolio-optimisation-in-python-e7844051e7f
# Thank you very much to user "The Rickest Ricky", i.e. tthustla on github for this beautiful code
# Changes were made to the API used (and thus data cleaning) since quandl API has been depreciated
# Further changes were made to send over data in JSON format for chart.js rather than using matplotlib
class MarkowitzChartData(APIView):
    def get(self, request, format=None):
        if request.user.is_authenticated:
            user_portfolio = Portfolio.objects.get(user=request.user)  # get portfolio associated with account
            stocklist = user_portfolio.stocks
            stocklist.sort()
            labels = stocklist

            today = dt.date.today().strftime('%m-%d-%Y')
            print(str(today))
            table = pd.DataFrame()
            data = get_data(stocklist[0], start_date='01/01/2016', end_date=today, index_as_date=False)
            dates = data["date"][::-1]

            for stock in stocklist:
                data = get_data(stock, start_date='01/01/2016', end_date=today, index_as_date=False)['adjclose'][
                       ::-1]
                table[stock.upper()] = pd.Series(data)
            table.set_index(dates, 'date', inplace=True)

            returns = table.pct_change()
            mean_returns = returns.mean()
            cov_matrix = returns.cov()
            num_portfolios = 25000
            risk_free_rate = 0.0178

            def portfolio_annualised_performance(weights, mean_returns, cov_matrix):
                returns = np.sum(
                    mean_returns * weights) * 252  # getting weighted returns for annualised trading days
                std = np.sqrt(np.dot(weights.T, np.dot(cov_matrix, weights))) * np.sqrt(
                    252)  # getting std deviation for the portfolio
                return std, returns

            def neg_sharpe_ratio(weights, mean_returns, cov_matrix,
                                 risk_free_rate):  # returns the negative sharpe ratio of the portfolio
                p_var, p_ret = portfolio_annualised_performance(weights, mean_returns, cov_matrix)
                return -(p_ret - risk_free_rate) / p_var

            def max_sharpe_ratio(mean_returns, cov_matrix, risk_free_rate):
                num_assets = len(mean_returns)
                args = (mean_returns, cov_matrix, risk_free_rate)
                constraints = ({'type': 'eq', 'fun': lambda x: np.sum(x) - 1})  # sum of weights must = 1
                bound = (0.0, 1.0)
                bounds = tuple(bound for asset in range(num_assets))
                result = sco.minimize(neg_sharpe_ratio, num_assets * [1. / num_assets, ], args=args,
                                      method='SLSQP', bounds=bounds,
                                      constraints=constraints)  # maximizing sharpe ratio
                return result

            def portfolio_volatility(weights, mean_returns,
                                     cov_matrix):  # just to separate the standard deviation for the function
                return portfolio_annualised_performance(weights, mean_returns, cov_matrix)[0]

            def min_variance(mean_returns, cov_matrix):
                num_assets = len(mean_returns)
                args = (mean_returns, cov_matrix)
                constraints = ({'type': 'eq', 'fun': lambda x: np.sum(x) - 1})
                bound = (0.0, 1.0)
                bounds = tuple(bound for asset in range(num_assets))

                result = sco.minimize(portfolio_volatility, num_assets * [1. / num_assets, ], args=args,
                                      method='SLSQP', bounds=bounds, constraints=constraints)

                return result

            def efficient_return(mean_returns, cov_matrix, target):
                num_assets = len(mean_returns)
                args = (mean_returns, cov_matrix)

                def portfolio_return(weights):
                    return portfolio_annualised_performance(weights, mean_returns, cov_matrix)[1]

                constraints = ({'type': 'eq', 'fun': lambda x: portfolio_return(x) - target},
                               {'type': 'eq', 'fun': lambda x: np.sum(x) - 1})
                bounds = tuple((0, 1) for asset in range(num_assets))
                result = sco.minimize(portfolio_volatility, num_assets * [1. / num_assets, ], args=args,
                                      method='SLSQP', bounds=bounds, constraints=constraints)
                return result

            def efficient_frontier(mean_returns, cov_matrix, returns_range):
                efficients = []
                for ret in returns_range:
                    efficients.append(efficient_return(mean_returns, cov_matrix, ret))
                return efficients


            max_sharpe = max_sharpe_ratio(mean_returns, cov_matrix, risk_free_rate)
            sdp, rp = portfolio_annualised_performance(max_sharpe['x'], mean_returns, cov_matrix)
            max_sharpe_allocation = pd.DataFrame(max_sharpe.x, index=table.columns, columns=['allocation'])
            max_sharpe_allocation.allocation = [round(i * 100, 2) for i in max_sharpe_allocation.allocation]
            max_sharpe_allocation = max_sharpe_allocation.T

            min_vol = min_variance(mean_returns, cov_matrix)
            sdp_min, rp_min = portfolio_annualised_performance(min_vol['x'], mean_returns, cov_matrix)
            min_vol_allocation = pd.DataFrame(min_vol.x, index=table.columns, columns=['allocation'])
            min_vol_allocation.allocation = [round(i * 100, 2) for i in min_vol_allocation.allocation]
            min_vol_allocation = min_vol_allocation.T

            # annualised volume and returns
            an_vol = np.std(returns) * np.sqrt(252)
            an_rt = mean_returns * 252

            print("-" * 80)
            print("Maximum Sharpe Ratio Portfolio Allocation\n")
            print("Annualised Return:", round(rp, 2))
            print("Annualised Volatility:", round(sdp, 2))
            print("\n")
            print(max_sharpe_allocation)
            print("-" * 80)
            print("Minimum Volatility Portfolio Allocation\n")
            print("Annualised Return:", round(rp_min, 2))
            print("Annualised Volatility:", round(sdp_min, 2))
            print("\n")
            print(min_vol_allocation)
            print("-" * 80)
            print("Individual Stock Returns and Volatility\n")
            # printing annualised volatility of each stock in portfolio
            for i, txt in enumerate(table.columns):
                print(txt, ":", "annualised return", round(an_rt[i], 2), ", annualised volatility:",
                      round(an_vol[i], 2))
            print("-" * 80)

            fig, ax = plt.subplots(figsize=(10, 7))
            ax.scatter(an_vol, an_rt, marker='o', s=200)

            for i, txt in enumerate(table.columns):
                ax.annotate(txt, (an_vol[i], an_rt[i]), xytext=(10, 0), textcoords='offset points')

            ax.scatter(sdp, rp, marker='*', color='r', s=500, label='Maximum Sharpe ratio')
            ax.scatter(sdp_min, rp_min, marker='*', color='g', s=500, label='Minimum volatility')

            target = np.linspace(rp_min, 0.34, 50)
            efficient_portfolios = efficient_frontier(mean_returns, cov_matrix, target)
            ax.plot([p['fun'] for p in efficient_portfolios], target, linestyle='-.', color='black',
                    label='efficient frontier')
            ax.set_title('Portfolio Optimization with Individual Stocks')
            ax.set_xlabel('annualised volatility')
            ax.set_ylabel('annualised returns')
            ax.legend(labelspacing=0.8)
            plt.savefig('efficient_frontier.png')

            data = {
                "labels": labels,
                "max_sharpe_allocation": max_sharpe_allocation,
                "max_sharpe_ret": round(rp,2),
                "max_sharpe_vol": round(sdp,2),
                "min_vol_allocation": min_vol_allocation,
                "min_vol_ret": round(rp_min, 2),
                "min_vol_vol": round(sdp_min, 2),
                #"dates": dates,
            }
            return Response(data)
        else:
            data = {
                "labels": [],
                "max_sharpe_allocation": [],
                "max_sharpe_ret": [],
                "max_sharpe_vol": [],
                "min_vol_allocation": [],
                "min_vol_ret": [],
                "min_vol_vol": [],
            }
            return Response(data)

#liquidating the portfolio
class LiquidateFormView(View):
    form_class = LiquidateForm
    template_name = 'personal/liquidate_form.html'

    #display blank form
    def get(self, request):
        form = self.form_class(None)
        return render(request, self.template_name, {'form': form})
    #process the form data
    def post(self, request):
        form = self.form_class(request.POST)

        if form.is_valid():

            # cleaned (normalized) data
            user = request.user
            password = form.cleaned_data['password']


            #returns User objects if credentials are correct

            user = authenticate(username=user.username, password=password)

            if user is not None: #user can only liquidate his/her portfolio if passwords were matched.
                if user.is_active:
                    user_portfolio = Portfolio.objects.get(user=user) #we reset the portfolio to empty here
                    user_portfolio.stocks = []
                    user_portfolio.save()

                    return redirect('index') # redirecting you back to the home page
            else:
                return render(request, self.template_name ,
                                          {'error_message': "<ul class='errorlist'> <li> Wrong password! </li> </ul>", 'form': form})

        return render(request, self.template_name, {'form': form})

# gets the prices for the data and returns it. Very similar to the first one, except we aren't giving data formatted for charts
# Rather, this data is formatted to go into a table.
class ReturnTableData(APIView):

    def get(self, request, format=None):
        if request.user.is_authenticated:
            user_portfolio = Portfolio.objects.get(user=request.user)  # get portfolio associated with account
            stocklist = user_portfolio.stocks
            stocklist.sort()
            labels = stocklist

            today = dt.date.today().strftime('%m-%d-%Y')
            week_ago = (dt.date.today()-dt.timedelta(days=7)).strftime('%m-%d-%Y')
            table = pd.DataFrame()
            data = get_data(stocklist[0], start_date=week_ago, end_date=today, index_as_date=False)
            dates = data["date"][::-1]
            # very similar to the first graph, we just switched dates and return different data
            for stock in stocklist:
                data = get_data(stock, start_date=week_ago, end_date=today, index_as_date=False)['adjclose'][::-1]
                table[stock.upper()] = pd.Series(data)
            table.set_index(dates, 'date', inplace=True)

            # we use pandas built in html converter to directly change the dataframe into html format
            data = {
                "labels": labels,
                "datasets": table.to_html(classes=["table", "table-bordered", "table-striped", "table-hover"]),
                "dates": dates,
            }
            return Response(data)
        else:
            data = {
                "labels" : [],
                "datasets": [],
                "dates": [],
            }
            return Response(data)

#gives the weekly return data
class ReturnRankingData(APIView):
    def get(self, request, format=None):
        if request.user.is_authenticated:
            user_portfolio = Portfolio.objects.get(user=request.user)  # get portfolio associated with account
            stocklist = user_portfolio.stocks
            stocklist.sort()
            labels = stocklist

            today = dt.date.today().strftime('%m-%d-%Y')
            week_ago = (dt.date.today() - dt.timedelta(days=7)).strftime('%m-%d-%Y')
            print(str(today))
            table = pd.DataFrame()
            data = get_data(stocklist[0], start_date=week_ago, end_date=today, index_as_date=False)
            dates = data["date"][::-1]

            for stock in stocklist:
                data = get_data(stock, start_date=week_ago, end_date=today, index_as_date=False)['adjclose'][::-1]
                table[stock.upper()] = pd.Series(data)
            table.set_index(dates, 'date', inplace=True)
            returns = pd.DataFrame(columns=['Stock', 'Weekly Return'])
            for column in table:
                last = table[column][-1]
                first = table[column][0]
                period_return = (last - first) / first
                toAppend = pd.DataFrame({'Stock': [column], 'Weekly Return': [round(period_return, 4)]})
                returns = returns.append(toAppend, ignore_index=True)
            returns = returns.sort_values(by=['Weekly Return'], ascending=False)
            returns.index = np.arange(1, len(returns) + 1)
            print(returns)

            data = {
                "labels": labels,
                "datasets": returns.to_html(classes=["table", "table-bordered", "table-striped", "table-hover"]),
                "dates": dates,
            }
            return Response(data)
        else:
            data = {
                "labels": [],
                "datasets": [],
                "dates": [],
            }
            return Response(data)
#similar to weekly, except its daily (not much to explain here)
class EarnerRankingData(APIView):
    def get(self, request, format=None):
        if request.user.is_authenticated:
            user_portfolio = Portfolio.objects.get(user=request.user)  # get portfolio associated with account
            stocklist = user_portfolio.stocks
            stocklist.sort()
            labels = stocklist

            today = dt.date.today().strftime('%m-%d-%Y')
            four_days_ago = (dt.date.today() - dt.timedelta(days=4)).strftime('%m-%d-%Y')
            print(str(today))
            table = pd.DataFrame()
            data = get_data(stocklist[0], start_date=four_days_ago, end_date=today, index_as_date=False)
            dates = data["date"][::-1]

            for stock in stocklist:
                data = get_data(stock, start_date=four_days_ago, end_date=today, index_as_date=False)['adjclose'][::-1]
                table[stock.upper()] = pd.Series(data)
            table.set_index(dates, 'date', inplace=True)
            returns = pd.DataFrame(columns=['Stock', 'Earnings'])
            for column in table:
                last = table[column][-1]
                first = table[column][0]
                period_return = (last - first) / first
                toAppend = pd.DataFrame({'Stock': [column], 'Earnings': [round(period_return, 4)]})
                returns = returns.append(toAppend, ignore_index=True)
            returns = returns.sort_values(by=['Earnings'], ascending=False)
            returns.index = np.arange(1, len(returns) + 1)
            print(returns)

            data = {
                "labels": labels,
                "datasets": returns.to_html(classes=["table", "table-bordered", "table-striped", "table-hover"]),
                "dates": dates,
            }
            return Response(data)
        else:
            data = {
                "labels": [],
                "datasets": [],
                "dates": [],
            }
            return Response(data)












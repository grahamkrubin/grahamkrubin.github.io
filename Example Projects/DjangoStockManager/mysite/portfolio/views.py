from django.shortcuts import render, redirect
from django.views.generic import View
from .forms import *
import quandl
import datetime as dt
from rest_framework.views import APIView
import pandas as pd
from rest_framework.response import Response
from yahoo_fin.stock_info import *
from .ytd import SimpleSymbolDownloader
# Create your views here.


#similar to the other forms, just this one is for adding to the portfolio objects
class AddStockFormView(View):
    form_class = AddStockForm
    template_name = 'portfolio/add_stock_form.html'

    def get(self, request):
        form = self.form_class(None)
        return render(request, self.template_name, {'form': form})
    def post(self, request):
        form = self.form_class(request.POST)

        if form.is_valid():
            user = request.user
            try:
                #create a new user portfolio if it doesn't already exist.
                # This shouldn't happen ever as the portfolios are created at user creation, but this is here just in case.

                user_portfolio = Portfolio.objects.get(user=user) #get portfolio associated with account
            except:
                portfolio = Portfolio(user=user)
                portfolio.save()
                pass

            user_portfolio = Portfolio.objects.get(user=user) #get portfolio associated with account
            stock = form.cleaned_data['stock_to_add']
            quandl.ApiConfig.api_key = 'fLUd1xbG8hjz3vMutU_s'
            data = quandl.get_table('WIKI/PRICES', ticker=stock,
                                    qopts={'columns': ['date', 'ticker', 'adj_close']},
                                    date={'gte': '2016-1-1', 'lte': dt.datetime.today().strftime('%Y-%m-%d')},
                                    paginate=True)

            list_of_stocks = user_portfolio.stocks
            if not data.empty:
                if stock not in list_of_stocks: #if stock wasn't already part of portfolio
                    user_portfolio.stocks.append(stock)
                    user_portfolio.save()
                    return redirect('index')
                else: #if it was already in the portfolio
                    return render(request, self.template_name,
                                  {'error_message': "<ul class='errorlist'> <li> Stock already in Portfolio! </li> </ul>",
                                   'form': form})
            else:
                return render(request, self.template_name, {'error_message': "<ul class='errorlist'> <li> Ticker not found! </li> </ul>",
                                   'form': form})
        return render(request, self.template_name, {'form': form})

class FindStockFormView(APIView):
    form_class = FindStockForm
    template_name = 'portfolio/find_stock_form.html'

    # display blank form
    def get(self, request):
        form = self.form_class(None)
        return render(request, self.template_name, {'form': form})

    #process the form data
    def post(self, request):

        form = self.form_class(request.POST)

        if form.is_valid():
            quandl.ApiConfig.api_key = 'fLUd1xbG8hjz3vMutU_s'
            stock = form.cleaned_data['stock_to_find']
            today = dt.date.today().strftime('%m-%d-%Y')
            week_ago = (dt.date.today() - dt.timedelta(days=7)).strftime('%m-%d-%Y')
            
           

            searcher = SimpleSymbolDownloader.SymbolDownloader("generic", str(stock))
            searcher.search()
            yeet = searcher.symbols
            # just_symbols = [tup[0] for tup in yeet]

            # checker = quandl.get_table('WIKI/PRICES', ticker=,
            #                         qopts={'columns': ['date', 'ticker', 'adj_close']},
            #                         date={'gte': '2016-1-1', 'lte': dt.datetime.today().strftime('%Y-%m-%d')},
            #                         paginate=True)

            returnMessage = ""
            if len(yeet) > 0:
                for entry in yeet:
                    returnMessage += ("Symbol: " + entry[0] + ", Company: " + entry[1] + '\n')
                return render(request, self.template_name, {'error_message': "<ul class='errorlist'> <li>" + str(returnMessage) + "</li> </ul>",'form': form, 'label': "", 'dataset': ""})
            else:
                return render(request, self.template_name, {'error_message': "<ul class='errorlist'> <li> Please enter a valid company name! </li> </ul>", 'form': form, 'label': "", 'dataset': ""})


            # thing = pd.read_html("https://finance.yahoo.com/lookup/")
            

            # if not checker.empty:
            #     result = get_data(stock, start_date=week_ago, end_date=today, index_as_date=True)
            #     return render(request, self.template_name, {'form':form,'dataset': result.to_html(classes=["table", "table-bordered", "table-striped", "table-hover"]), 'label':stock.upper()})
            # else:
            #     return render(request, self.template_name, {'error_message': "<ul class='errorlist'> <li> Ticker not found in search! </li> </ul>",
            #                        'form': form, 'label': "", 'dataset': ""})

        return render(request, self.template_name, {'form': form})


from django.contrib.auth.models import User
from portfolio.models import Portfolio
from django import forms

class LoginForm(forms.Form):
    username = forms.CharField(widget=forms.TextInput(attrs={'placeholder': 'Username'}))
    password = forms.CharField(widget=forms.PasswordInput(attrs={'placeholder': 'Password'}))
    fields = ['username', 'password']


class AddStockForm(forms.Form):
    stock_to_add = forms.CharField(widget=forms.TextInput(attrs={'placeholder': 'Ticker'}))
    fields = ['stock_to_add']

    # class Meta:
    #     model = Portfolio
    #     fields =
class FindStockForm(forms.Form):
    stock_to_find = forms.CharField(widget=forms.TextInput(attrs={'placeholder': 'Ticker'}))
    fields = ['stock_to_find']
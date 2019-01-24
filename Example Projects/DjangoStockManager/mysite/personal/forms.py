from django.contrib.auth.models import User
from django import forms
#user creation form; only one that needs meta since its creating stuff, based on built in User model
class UserForm(forms.ModelForm):
    password = forms.CharField(widget=forms.PasswordInput)


    class Meta:
        model = User
        fields = ['username', 'email', 'password']
#login form
class LoginForm(forms.Form):
    username = forms.CharField(widget=forms.TextInput(attrs={'placeholder': 'Username'}))
    password = forms.CharField(widget=forms.PasswordInput(attrs={'placeholder': 'Password'}))
    fields = ['username', 'password']
#password changing form
class PassChangeForm(forms.Form):
    password = forms.CharField(widget=forms.PasswordInput(attrs={'placeholder': 'New Password'}))
    password_check = forms.CharField(widget=forms.PasswordInput(attrs={'placeholder': 'Password Again'}))
    fields = ['password', 'password_check']
#liquidation form
class LiquidateForm(forms.Form):
    password = forms.CharField(widget=forms.PasswordInput(attrs={'placeholder': 'Password'}))
    fields = ['password']


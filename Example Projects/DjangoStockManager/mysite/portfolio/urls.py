
from . import views
from personal import views as personal_views
from django.urls import re_path


urlpatterns = [
    re_path(r'^$',  personal_views.index, name='index'),
    re_path(r'^addstock/$', views.AddStockFormView.as_view(), name='add_stock'), #url for addstock, put it with portfolio stuff
    re_path(r'^findstock/$', views.FindStockFormView.as_view(), name='find_stock') #url for findstock
    ]

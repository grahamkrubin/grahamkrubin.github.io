from django.urls import re_path
from . import views

urlpatterns = [
        re_path(r'^$', GenericView.as_view(template_name="plot/plot.html"))
    ]

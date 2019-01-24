from django.db import models
from django_mysql.models import ListCharField
from django.contrib.auth.models import User
# Create your models here.

class Portfolio(models.Model):
    stocks = ListCharField(
        base_field=models.CharField(max_length=10),
        max_length=200,
        # error_messages={
        #     'unique': _("Stock already exists in portfolio!")
        # }
    )
    # compare_img = models.ImageField()
    # returns_img = models.ImageField()
    # markowitz_img = models.ImageField()
    user = models.ForeignKey(User, on_delete=models.CASCADE)

    def __str__(self):
        return self.user.username


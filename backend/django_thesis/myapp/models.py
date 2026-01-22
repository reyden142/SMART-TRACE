from django.db import models

# Create your models here.
class Users(models.Model):
    id = models.CharField(max_length=6, primary_key=True)

from django.urls import path
from . import views

urlpatterns = [
    path("", views.home, name="home"),
    path("manage_users/", views.manage_users, name="manage_users"),
    path("users/", views.users, name="users")
    # path('login/', views.login_view, name='login')
]
#1

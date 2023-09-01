from django.shortcuts import render, HttpResponse
#import serial
#from routeros_api import RouterOsApiPool
#from librouteros import connect

##    ser = serial.Serial("COM12",9600)
##    data = ser.readline().decode().strip()

##    ser.close()

   ##g return render(request, "arduino/index.html",{
   ##     "data":d
   ## })
def home(request):
    return render(request, "home.html")

def manage_users(request):
    return render(request, "manage_users.html")

def users(request):
    return render(request, "users.html")

def users(request):
    return render(request, "logout.html")

#hjbbjhbjh
#ako ni
#hhh

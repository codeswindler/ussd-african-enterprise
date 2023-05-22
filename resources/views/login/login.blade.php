@extends('layouts.loginLayout')

<div class="container form-container">

    <div class=" d-flex justify-content-center align-items-center">

    <form method="POST" action=/login class="formdata w-50 card shadow rounded p-4">
        @csrf
    <div class="">
        <h3 class="text-center form-title">ADMIN LOGIN</h3>
    </div>
    
        <div class="form-floating mb-3">
            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email Address" required>
            <label for="email">Email</label>
            
        </div>
  
    
        <div class="form-floating mb-3">
            
            <input type="password" id="password" name="password" class="form-control" required  placeholder="Password">
            <label for="password">Password</label>
        </div>
    

        <div class="form-group text-center">
            <button type="submit" class="button">Login</button>
        </div>
    </form>

    </div>

</div>



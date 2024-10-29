=== Aion Assists - Customer Service ===
Tags: aionassists
Stable tag: 1.0.1
Tested up to: 6.5
Requires at least: 5.0
Requires PHP: 7.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The **Aion Assists** plugin is an **AI-powered customer service solution for your WooCommerce stores**. This customer service chatbot plugin, powered by the GPT-based OpenAI API, will help you resolve all shopping processes and support requests for your users.

== Description ==
In addition to numerous features such as viewing order status, changing addresses, adding notes, and querying payment methods, users can easily learn store-specific details (such as working hours, shipping company, contact information, and many other features).

Unlock the full potential of your customer service with [Aion Assists – Customer Service Premium packages](https://www.aionisys.com/pricing/) and gain access to all premium features.

You can view conversations between your assistant and customers, easily access customer data, summarize the entire session with a single click, and activate and personalize nearly 100 features of your fully automated AI-powered chatbot.

## Features

- Show Order Status
- Show Order Creation Date
- Show Order Refunds
- List All Payment Gateways
- List All Shipping Zones
- Add a Marketing Message
- Display Session Quantity
- Show Order Items
- Show Shipping Address
- Show Billing Address
- Specify Customer Service Contact
- Specify Free Shipping Threshold
- Display Messages Quantity
- Display Successful Operation Quantity
- Show Product Stock
- Update Shipping Address
- Update Billing Address
- Update Order Note
- Product Marketing
- Specify the Shipping Company
- Specify the Return Policy
- Specify the Warranty Conditions
- Specify Shipment Date
- Specify the Refund Deposit Date
- Display Chat Content
- Display Successful Operation Chat Content
- Message Backups Screen
- Session/Chat Summarization

The **Aion Assists** plugin allows customers/users to ask questions and receive answers in all languages globally on the front end. It prioritizes your customer's inquiries with a human-like attitude, correctly interpreting and responding even if the customer makes critical spelling and grammatical errors. Additionally, your plugin features an admin panel translated into six languages: English, German, Italian, Spanish, French, and Turkish. This enables stronger data management capabilities.

### Features to be added in the next version:
- Transfer customer information to MailChimp from interactions with your Aion Assists plugin
- Delete sessions and conversations
- Send campaign and announcement notifications to your customers from within the chatbot
- Special toggle and pop-up notifications

== Installation ==
1. Download the plugin.
2. Upload it to your WordPress site.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to the "Setup Wizard" page under the plugin menu.
5. If you want to use the plugin as a free user, select the "Free" user type and enter your company name and OpenAI API key. If you want to start a free trial, start your trial on [our website](https://www.aionisys.com).

**NOTE:** If you are a premium or trial user, you do not need an OpenAI API key, and there are no OpenAI charges for you. These costs are entirely borne by Aionisys.

== Frequently Asked Questions ==
Does an OpenAI key need to be added for free users?
- Yes, you can use the plugin with an OpenAI key for free license usage. For premium users, you do not need to enter an OpenAI API key. Premium users are currently not able to use their own OpenAI API key exclusively.

Which features can I choose to be visible to users?
- Yes, you can select which features of the plugin can be queried and viewed by users.

Can I set personalized settings for my company? Can I fine-tune the language model?
- You can add many details specific to your company and present them to users during chats based on their requests. However, fine-tuning at the developer level is not currently possible.

== Screenshots ==
1. Example from the plugin #1
2. Example from the plugin #2
3. Example from the plugin #3
4. Example from the plugin #4
5. Example from the plugin #5
6. Example from the plugin #6
7. Example from the plugin #7

== 3rd Party Services ==

This plugin utilizes a third-party service, Aion Assists, for certain functionalities. Below are the details of how this service is used:

- Authentication Endpoint: 
  - URL: (https://aionassistsapp.azurewebsites.net/auth)
  - Purpose: Generates a token specific to your license key and basic company information, allowing it to be stored in your site's local database for authentication purposes.

- Settings Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/auth/settings)
  - Purpose: Saves chatbot settings permissions.

- Preferences Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/auth/preferences)
  - Purpose: Saves chat response preferences.

- Sessions Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/auth/sessions)
  - Purpose: Lists all sessions recorded in the database.

- Get Sessions Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/auth/get-sessions)
  - Purpose: Retrieves details of specific sessions from the database.

- Get Messages Summary Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/auth/get-messages-summary?sessionId=${sessionId}&licenseId=${licenseId})
  - Purpose: Fetches all conversations associated with the selected session from the database.

- Start Session Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/customer/start-session)
  - Purpose: Facilitates the process of obtaining a session number to initiate the chat service for customers.

- Customer Endpoint:
  - URL: (https://aionassistsapp.azurewebsites.net/customer)
  - Purpose: Used by customers to write to and receive responses from the chat service.

It's important for users to be aware of this integration for transparency and legal compliance. Please review the terms of use and privacy policies of Aion Assists. [Privacy Policy](https://www.aionisys.com/privacy-policy/).

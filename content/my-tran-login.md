---
title: "MyTran Login"
mediawiki:
  title: "MyTran Login"
  revisions: 1
  last_edited: "2024-01-03"
  contributors: ["Lostarch"]
---

`            Constant:               myTran_Login`

Initiator: Client

Start login sequence with the server (see *Transaction Sequences*).

Fields used in the request:

<div class="table-wrap">
<table class="wikitable" border="1" cellpadding="4" cellspacing="0">
<tbody>
<tr>
<td style="width:.45in;"><p><strong>ID</strong></p></td>
<td style="width:96px;"><p><strong>Field Name</strong></p></td>
<td style="width:451px;"><h3 id="note">Note</h3></td>
</tr>
<tr>
<td style="width:.45in;"><p>105</p></td>
<td style="width:96px;"><p>User login</p></td>
<td style="width:451px;"></td>
</tr>
<tr>
<td style="width:.45in;"><p>106</p></td>
<td style="width:96px;"><p>User password</p></td>
<td style="width:451px;"></td>
</tr>
<tr>
<td style="width:.45in;"><p>160</p></td>
<td style="width:96px;"><p>Version</p></td>
<td style="width:451px;"><p>Currently 151</p></td>
</tr>
</tbody>
</table>
</div>

Fields used in the reply:

<div class="table-wrap">
<table class="wikitable" border="1" cellpadding="4" cellspacing="0">
<tbody>
<tr>
<td style="width:.45in;"><h4 id="id">ID</h4></td>
<td style="width:96px;"><p><strong>Field Name</strong></p></td>
<td style="width:451px;"><h3 id="note_1">Note</h3></td>
</tr>
<tr>
<td style="width:.45in;"><p>160</p></td>
<td style="width:96px;"><p>Version</p></td>
<td style="width:451px;"></td>
</tr>
</tbody>
</table>
</div>

If Version is \>= 151, additional fields are included:

<div class="table-wrap">
<table class="wikitable" border="1" cellpadding="4" cellspacing="0">
<tbody>
<tr>
<td style="width:.45in;"><h4 id="id_1">ID</h4></td>
<td style="width:96px;"><p><strong>Field Name</strong></p></td>
<td style="width:451px;"><h3 id="note_2">Note</h3></td>
</tr>
<tr>
<td style="width:.45in;"><p>161</p></td>
<td style="width:96px;"><p>Banner ID</p></td>
<td style="width:451px;"><p>Used for making HTTP request to get banner</p></td>
</tr>
<tr>
<td style="width:.45in;"><p>162</p></td>
<td style="width:96px;"><p>Server name</p></td>
<td style="width:451px;"><p>Server name string</p></td>
</tr>
</tbody>
</table>
</div>

If server version is \< 151, client sends Set Client User Info (304) transaction with only User Name (102) and User Icon ID (104) fields used, and does not expect a reply. It does not expect agreement to be received (109). Subsequently, it sends Get User Name List (300) request, followed by Get File Name List (200) or Get News Category Name List (370), depending on user preferences. After that, a banner is requested from HTTP server.

package com.example.mindcare.ui.screens

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.icons.Icons
import androidx.compose.material3.icons.filled.ArrowBack
import androidx.compose.material3.icons.filled.CameraAlt
import androidx.compose.material3.icons.filled.Photo
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.mindcare.model.Mood
import com.example.mindcare.ui.components.MoodSelector

@Composable
fun PhotoEntryScreen(
    onBack: () -> Unit
) {
    var caption by remember { mutableStateOf("") }
    var selectedMood by remember { mutableStateOf<Mood?>(Mood.GOOD) }

    Column(modifier = Modifier.fillMaxSize()) {
        TopAppBar(
            title = { Text("Photo entry") },
            navigationIcon = {
                IconButton(onClick = onBack) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                }
            }
        )
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(20.dp)
        ) {
            Text(
                text = "Attach a photo",
                style = MaterialTheme.typography.titleLarge
            )
            Spacer(modifier = Modifier.height(12.dp))
            androidx.compose.foundation.layout.Row {
                Button(onClick = { /* mock camera */ }) {
                    Icon(Icons.Default.CameraAlt, contentDescription = null)
                    Spacer(modifier = Modifier.height(0.dp))
                    Text("Camera")
                }
                Spacer(modifier = Modifier.height(0.dp).padding(horizontal = 8.dp))
                Button(onClick = { /* mock gallery */ }) {
                    Icon(Icons.Default.Photo, contentDescription = null)
                    Spacer(modifier = Modifier.height(0.dp))
                    Text("Gallery")
                }
            }
            Spacer(modifier = Modifier.height(16.dp))
            Text(text = "Mood tag")
            MoodSelector(selected = selectedMood, onMoodSelected = { selectedMood = it })

            Spacer(modifier = Modifier.height(16.dp))
            OutlinedTextField(
                value = caption,
                onValueChange = { caption = it },
                label = { Text("Caption") },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(120.dp)
            )
            Spacer(modifier = Modifier.height(20.dp))
            Button(
                onClick = { onBack() },
                modifier = Modifier.fillMaxWidth()
            ) {
                Text("Save (mock)")
            }
        }
    }
}


